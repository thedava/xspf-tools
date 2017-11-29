node {
    checkout scm
    stage('Build') {
        sh 'bash bin/composer.sh'
        sh 'php composer.phar install --prefer-dist --no-progress --no-suggest --no-ansi'
    }
    stage('Test') {
        sh 'php vendor/bin/phpunit --disallow-test-output'
    }
}
