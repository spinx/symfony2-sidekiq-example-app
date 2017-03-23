require 'sidekiq-ent'
require 'sidekiq-ent/web'
require 'sidekiq-statistic'

run Rack::URLMap.new(
	'/' => Sidekiq::Web
);