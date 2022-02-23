#!/bin/bash
#App
docker build -f ./src/provisioning/app.prod.dockerfile -t docker-registry.local:5001/app-instance-alumni:1.0.1 ./src
docker push docker-registry.local:5001/app-instance-alumni:1.0.1
