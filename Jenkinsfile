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
                sh 'php composer.phar install --prefer-dist --no-progress --no-ansi'
                sh 'php composer.phar console'
            }
        }
        stage('Validate') {
            failFast true
            parallel {
                stage('Test console') {
                    steps {
                        sh 'php console.php version -v'
                    }
                }
                stage('PHP Lint') {
                    steps {
                        sh 'find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -l1 -0 php -l'
                    }
                }
                stage('Lint') {
                    steps {
                        sh 'find . -name "*.sh"   -not -path "*vendor*" -print0 | xargs -l1 -0 shellcheck -s bash'
                        sh 'find . -name "*.json" -not -path "*vendor*" -print0 | xargs -l1 -0 jsonlint'
                        sh 'find . -name "*.yml"  -not -path "*vendor*" -print0 | xargs -l1 -0 yamllint'
                    }
                }
                stage('PHPUnit') {
                    steps {
                        sh '''
                            php -d xdebug.mode=coverage vendor/bin/phpunit --disallow-test-output \
                            --log-junit "./test/data/junit.xml" \
                            --coverage-clover "./test/data/clover.xml"
                        '''
                        junit '**/test/data/junit.xml'
                        step([$class: 'CloverPublisher', cloverReportDir: './test/data', cloverReportFileName: 'clover.xml'])
                    }
                }
                stage('Test Phar') {
                    steps {
                        // Build phar
                        sh 'php composer.phar build-dev'
                        sh 'php build/xspf.phar version -v'

                        // Test self-update
                        sh 'php build/xspf.phar self-update'
                        sh 'php build/xspf.phar self-update -f'

                        sh 'rm -f build/xspf.phar'
                    }
                }
            }
        }
    }
}
