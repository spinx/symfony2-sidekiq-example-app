symfony2-sidekiq-example-app
============================
## What is it ?
Generate Sidekiq workers from symfony2 services, queue jobs and let Sidekiq process them by calling worker:exec command.

## Setup
```bash
composer install
gem install bundler
bundle install
```

### Redis Configuration
app/config/services.yml

### Pushing to a queue
https://github.com/spinx/symfony2-sidekiq-example-app/blob/master/src/DLabs/UserBundle/Command/UserEnqueueCommand.php

### Queue handler 
https://github.com/spinx/symfony2-sidekiq-example-app/blob/master/src/DLabs/UserBundle/Resources/config/queue_handler.yml



Running Sidekiq
===============
#### Run Sidekiq
```bash
bin/sidekiq
```

#### Run Sidekiq web interface
```bash
bin/sidekiq-web
```

## License
MIT. Use as you wish. Let me know if you find it useful!
