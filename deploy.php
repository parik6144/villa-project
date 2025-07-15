<?php
namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'git@github.com:dits-agency/laravel-booking.git');
set('keep_releases', 15);
set('http_user', false);
set('local_root', '.');
set('shared_dirs', ['storage']);
set('shared_files', ['.env']);
set('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

host('staging')
    ->setHostname('49.13.172.241')
    ->setRemoteUser('dev')
    ->set('deploy_path', '~/web/booking.dits.md/public_html')
    ->set('forward_agent', true);

host('test')
    ->setHostname('167.235.140.198')
    ->setRemoteUser('dev')
    ->set('deploy_path', '~/web/test.villa4you.club/public_html')
    ->set('forward_agent', true);

host('prod')
    ->setHostname('167.235.140.198')
    ->setRemoteUser('webprod')
    ->set('deploy_path', '~/web/villa4you.club/public_html')
    ->set('forward_agent', true);

task('build', function () {
    run('uptime');
});

task('deploy:build', function () {
    run('cd {{release_path}} && source ~/.nvm/nvm.sh && nvm use v20.15.0 && npm install');
    run('cd {{release_path}} && source ~/.nvm/nvm.sh && nvm use v20.15.0 && npm run build');
});

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:build',
    'artisan:storage:link',
    'artisan:config:cache',
    'artisan:route:cache',
    'artisan:view:cache',
    'artisan:event:cache',
    'artisan:migrate',
    'deploy:publish',
]);

after('deploy:symlink', 'artisan:optimize');

after('deploy:failed', 'deploy:unlock');
