<?php
require '/proj/_harness.php';
$app = make_app();
$k = $app->make(Illuminate\Contracts\Http\Kernel::class);
$P=0;$F=0;$fails=[];
function ok($n,$c,$d=''){global $P,$F,$fails; if($c)$P++; else {$F++;$fails[]="$n".($d?" :: $d":'');}}
function go($k,$m,$u,$h=[],$json=true){$r=Illuminate\Http\Request::create($u,$m);$r->headers->remove('Accept-Language');
 if($json)$r->headers->set('Accept','application/json');foreach($h as $a=>$b)$r->headers->set($a,$b);return $k->handle($r);}

// --- invented route removed ---
$r=go($k,'GET','/api/v1/session');
ok('invented /api/v1/session no longer exists (404)',$r->getStatusCode()===404,(string)$r->getStatusCode());
ok('unmatched api -> API_UNSUPPORTED_ROUTE',json_decode($r->getContent(),true)['error']['code']==='API_UNSUPPORTED_ROUTE');

// --- only documented auth endpoints may exist; none yet ---
$uris=[];foreach($app->make('router')->getRoutes() as $rt)$uris[]=$rt->uri();
ok('no undocumented api endpoint registered',count(array_filter($uris,fn($u)=>str_starts_with($u,'api/v1/')&&$u!=='api/v1/{fallbackPlaceholder}'))===0,implode(',',array_filter($uris,fn($u)=>str_starts_with($u,'api/v1/'))));
ok('no notification route',count(array_filter($uris,fn($u)=>str_contains($u,'notification')))===0);

// --- sanctum guard resolves ---
ok('sanctum guard configured',config('auth.guards.sanctum.driver')==='sanctum');

// --- envelope + headers intact ---
ok('X-Content-Type-Options',$r->headers->get('X-Content-Type-Options')==='nosniff');
ok('X-Frame-Options',$r->headers->get('X-Frame-Options')==='SAMEORIGIN');
ok('Referrer-Policy',$r->headers->get('Referrer-Policy')==='strict-origin-when-cross-origin');
ok('X-Request-Id',(bool)$r->headers->get('X-Request-Id'));
foreach(['exception','trace','file','line'] as $key) ok("no '$key' in error body",!array_key_exists($key,json_decode($r->getContent(),true)));
foreach(['/vsrc','/proj','Illuminate\\','Stack trace'] as $leak) ok("no leak '$leak'",!str_contains($r->getContent(),$leak));

// --- shell + i18n ---
$r=go($k,'GET','/',[],false);
ok('shell ar default',str_contains($r->getContent(),'lang="ar"'));
ok('shell rtl default',str_contains($r->getContent(),'dir="rtl"'));
ok('no favicon 404 ref',!str_contains($r->getContent(),'favicon'));
$r=go($k,'GET','/',['Accept-Language'=>'en'],false);
ok('shell en',str_contains($r->getContent(),'lang="en"'));
ok('shell ltr',str_contains($r->getContent(),'dir="ltr"'));
ok('deep link ok',go($k,'GET','/teacher-workspace/groups',[],false)->getStatusCode()===200);
ok('api not shadowed by shell',str_starts_with(trim(go($k,'GET','/api/v1/x')->getContent()),'{'));

// --- translations complete ---
foreach(['ar','en'] as $loc) foreach(App\Support\Api\ErrorCode::cases() as $c)
  ok("[$loc] {$c->value}",trans($c->messageKey(),[],$loc)!==$c->messageKey());
ok('ar is default',config('app.locale')==='ar' && config('app.fallback_locale')==='ar');

// --- deployment readiness ---
ok('root .htaccess exists',is_file('/proj/.htaccess'));
$ht=(string)@file_get_contents('/proj/.htaccess');
foreach(['app','config','database','storage','vendor'] as $d) ok(".htaccess denies $d",str_contains($ht,$d));
ok('public .htaccess front controller',str_contains((string)file_get_contents('/proj/public/.htaccess'),'index.php'));
foreach(['Dockerfile','docker-compose.yml','compose.yaml','Procfile'] as $f) ok("no $f",!is_file("/proj/$f"));
ok('robots disallows all',str_contains((string)file_get_contents('/proj/public/robots.txt'),'Disallow: /'));

// --- structure survives clone ---
$bf=['Authentication','Authorization','PlatformAdministration','TeacherWorkspace','EducationalGrades','Groups','Students','Parents','Attendance','Homework','Lessons','Exams','Reports','Payments','Subscriptions','Users','Settings','Files','Archive','AuditLog'];
foreach($bf as $f) ok("tracked app/Features/$f",is_file("/proj/app/Features/$f/.gitkeep"));
$ff=['authentication','platform-administration','teacher-workspace','educational-grades','groups','students','parents','attendance','homework','lessons','exams','reports','payments','subscriptions','users','settings','files','archive','audit-log'];
foreach($ff as $f) ok("tracked js/features/$f",is_file("/proj/resources/js/features/$f/.gitkeep"));
foreach(['assets','auth','components/primitives','components/shared','layouts'] as $d) ok("tracked js/$d",is_file("/proj/resources/js/$d/.gitkeep"));

// --- no separation ---
foreach(['frontend','backend','laravel_app','deployment'] as $d) ok("no $d/",!is_dir("/proj/$d"));
ok('react under resources/js',is_file('/proj/resources/js/app/main.tsx'));

// --- env template secrets empty ---
$env=(string)file_get_contents('/proj/.env.example');
foreach(['APP_KEY=','DB_PASSWORD=','MAIL_PASSWORD='] as $key) ok("$key empty in template",(bool)preg_match('/^'.preg_quote($key,'/').'\s*$/m',$env));
foreach(['REDIS_','AWS_','PUSHER_','DOCKER'] as $bad) ok("template free of $bad",!str_contains($env,$bad));

printf("\nPASSED: %d   FAILED: %d\n",$P,$F);
foreach($fails as $f) echo "  FAIL: $f\n";
exit($F?1:0);
