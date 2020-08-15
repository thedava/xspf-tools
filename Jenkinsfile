pipeline {
    agent {
        node {
            label 'php'
        }
    }
    stages {
        stage('Build') {
            steps {
                sh 'bash bin/composer.sh'
                sh 'php composer.phar global require hirak/prestissimo --no-ansi --no-progress --no-suggest'
                sh 'php composer.phar install --prefer-dist --no-progress --no-suggest --no-ansi'
            }
        }
        stage('Validate') {
            parallel {
                stage('PHP Lint') {
                    steps {
                        sh 'find . -name "*.php"   -not -path "./vendor/*" -print0 | xargs -l1 -0 php -l'
                    }
                }
                stage('Lint') {
                    steps {
                        sh 'find . -name "*.sh"    -not -path "./vendor/*" -print0 | xargs -l1 -0 shellcheck -s bash'
                        sh 'find . -name "*.json"  -not -path "*vendor*" -print0   | xargs -l1 -0 jsonlint'
                    }
                }
                stage('PHPUnit') {
                    steps {
                        sh '''
                            php vendor/bin/phpunit --disallow-test-output \
                            --log-junit "./test/data/junit.xml" \
                            --coverage-clover "./test/data/clover.xml"
                        '''
                        junit '**/test/data/junit.xml'
                        step([$class: 'CloverPublisher', cloverReportDir: './test/data', cloverReportFileName: 'clover.xml'])
                    }
                }
                stage('Symfony CodeChecker') {
                    steps {
                      sh label: 'Download and execute symfony security checker', script:  '''
                        curl -sS https://get.sensiolabs.org/security-checker.phar -o security-checker.phar
                        php security-checker.phar security:check composer.lock --no-ansi --format simple
                      '''
                    }
                }
                stage('Test Build Phar') {
                    steps {
                        sh 'php composer.phar build-dev'
                        sh 'php build/xspf.phar version'
                    }
                }
            }
        }
    }
}
