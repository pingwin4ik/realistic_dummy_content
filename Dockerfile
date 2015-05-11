# Our Dockerfile needs to be named "Dockerfile" because we are using CircleCI
# and it does not allow us to use the -f flag to specify another filename (for
# example Dockerfile-test). (See also ./scripts/test.sh).

FROM alberto56/docker-drupal:8.0.x-dev-1.0-8.0.0-beta10

ADD . ./srv/drupal/www/modules/realistic_dummy_content/

EXPOSE 80

RUN cd ./srv/drupal/www && drush --uri=http://127.0.0.1 test-run "Realistic dummy content"
