require 'sidekiq'
require 'sidekiq/web'
    require 'sidekiq-statistic'

run Rack::URLMap.new(
	'/' => Sidekiq::Web
);