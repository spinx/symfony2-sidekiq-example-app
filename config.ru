require 'sinatra/base'
require 'sidekiq'
require 'sidekiq/api'
require 'sidekiq/cron'
require 'sidekiq/web'
require 'sidekiq/cron/web'

run Rack::URLMap.new(
	'/' => Sidekiq::Web
);