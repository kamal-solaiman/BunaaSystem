<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduled Tasks
|--------------------------------------------------------------------------
|
| Scheduled work runs through the Laravel Scheduler, triggered by a single
| cPanel Cron Job (AI_DOCS/21_Background_Jobs.md; 26_Deployment_Plan.md).
| Queued work uses the Database Queue — no daemon, no Redis, no supervisor.
|
| Foundation phase: no scheduled commands are registered yet.
|
*/
