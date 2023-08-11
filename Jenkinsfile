pipeline {

  agent none

  environment {
    DOCKER_IMAGE_PHP = "truongdinhtrongctim/php-laravel"
    DOCKER_IMAGE_NGINX = "truongdinhtrongctim/nginx-laravel"
  }


  stages {

    stage('Sonarqube') {
      agent { node {label 'master'}}
        environment {
            scannerHome = tool 'SonarQubeScanner'
        }
        steps {
            withSonarQubeEnv('sonarqube') {
                sh "${scannerHome}/bin/sonar-scanner"
            }
            timeout(time: 10, unit: 'MINUTES') {
                waitForQualityGate abortPipeline: true
            }
        }
    }
  
    stage("build php-laravel") {
      agent { node {label 'master'}}
      environment {
        DOCKER_TAG="${GIT_BRANCH.tokenize('/').pop()}-${GIT_COMMIT.substring(0,7)}"
      }
      steps {
        sh "echo build php - laravel"
        sh "docker build -t ${DOCKER_IMAGE_PHP}:${DOCKER_TAG} -f Dockerfile.php . "
        sh "docker tag ${DOCKER_IMAGE_PHP}:${DOCKER_TAG} ${DOCKER_IMAGE_PHP}:latest"
        sh "docker image ls | grep ${DOCKER_IMAGE_PHP}"
        withCredentials([usernamePassword(credentialsId: 'dockerhub-token', usernameVariable: 'DOCKER_USERNAME', passwordVariable: 'DOCKER_PASSWORD')]) {
            sh 'echo $DOCKER_PASSWORD | docker login --username $DOCKER_USERNAME --password-stdin'
            sh "docker push ${DOCKER_IMAGE_PHP}:${DOCKER_TAG}"
            sh "docker push ${DOCKER_IMAGE_PHP}:latest"
        }

        //clean to save disk
        sh "docker image rm ${DOCKER_IMAGE_PHP}:${DOCKER_TAG}"
        sh "docker image rm ${DOCKER_IMAGE_PHP}:latest"
      }
    }

    stage("build nginx-laravel") {
      agent { node {label 'master'}}
      environment {
        DOCKER_TAG="${GIT_BRANCH.tokenize('/').pop()}-${GIT_COMMIT.substring(0,7)}"
      }
      steps {
        sh "echo build nginx - web"
        sh "docker build -t ${DOCKER_IMAGE_NGINX}:${DOCKER_TAG} -f ./nginx/Dockerfile.nginx . "
        sh "docker tag ${DOCKER_IMAGE_NGINX}:${DOCKER_TAG} ${DOCKER_IMAGE_NGINX}:latest"
        sh "docker image ls | grep ${DOCKER_IMAGE_NGINX}"
        withCredentials([usernamePassword(credentialsId: 'dockerhub-token', usernameVariable: 'DOCKER_USERNAME', passwordVariable: 'DOCKER_PASSWORD')]) {
            sh 'echo $DOCKER_PASSWORD | docker login --username $DOCKER_USERNAME --password-stdin'
            sh "docker push ${DOCKER_IMAGE_NGINX}:${DOCKER_TAG}"
            sh "docker push ${DOCKER_IMAGE_NGINX}:latest"
        }

        //clean to save disk
        sh "docker image rm ${DOCKER_IMAGE_NGINX}:${DOCKER_TAG}"
        sh "docker image rm ${DOCKER_IMAGE_NGINX}:latest"
      }
    }
  }

  post {
    success {
      echo "SUCCESSFUL"
    }
    failure {
      echo "FAILED"
    }
  }
}