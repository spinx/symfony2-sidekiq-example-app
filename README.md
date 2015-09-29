symfony2-sidekiq-example-app
============================

## Setup
```bash
composer install
gem install bundler
bundle install
```

### Redis Configuration
app/config/services.yml

### Pushing to a queue
https://github.com/spinx/symfony2-sidekiq-example-app/blob/master/src/DLabs/UserBundle/Command/UserQueuePushCommand.php

### Queue handler 
https://github.com/spinx/symfony2-sidekiq-example-app/blob/master/src/DLabs/UserBundle/Resources/config/queue_handler.yml



Running Sidekiq
===============
#### Run Sidekiq
```bash
sidekiq -r ./sidekiq.rb -C ./sidekiq.yml -c 1
```

#### Run Sidekiq web interface
```bash
rackup -o 0.0.0.0
```

## License
MIT. Use as you wish. Let me know if you find it useful!