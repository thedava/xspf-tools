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
                sh 'make build-console'
            }
        }

        // Basic validation (linting, unittests, ...) and building (xspf.phar)
        stage('Q&A: Basic') {
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

                stage('Build Phar') {
                    steps {
                        // Build phar
                        sh 'make build-phar'
                    }
                }
            }
        }

        // Specialized validation of xspf.phar
        stage('Q&A: Assets') {
            parallel {
                stage('Build & validate bundles') {
                    steps {
                        // Build bundles
                        sh 'make build-bundles'
                    }
                }

                stage('Validate Phar') {
                    steps {
                        // Validate phar
                        sh 'php build/xspf.phar version -v'
                    }
                }

                stage('Self-Update Phar') {
                    steps {
                        sh 'cp -f build/xspf.phar build/self-update/'

                        dir('build/self-update/') {
                            // Create copy (so other tests won't be affected)
                            sh 'cp -f ./../xspf.phar ./'

                            // Test self-update
                            sh 'php xspf.phar self-update'
                            sh 'php xspf.phar self-update -f'
                        }
                    }
                }

                stage('Self-Update Phar (BETA)') {
                    steps {
                        sh 'mkdir -p build/self-update-beta/'

                        dir('build/self-update-beta/') {
                            // Create copy (so other tests won't be affected)
                            sh 'cp -f ./../xspf.phar ./'

                            // Test self-update
                            sh 'php beta/xspf.phar self-update -b'
                            sh 'php beta/xspf.phar self-update -b -f'
                        }
                    }
                }

                stage('Create index') {
                    steps {
                        sh 'php build/xspf.phar index:create ./ -o build/index.xd'

                        sh 'test -f build/index.xd'
                    }
                }
            }
        }
    }

    post {
        always  {
            script {
                sh 'rm -rfv build/*'
            }
        }
    }
}
