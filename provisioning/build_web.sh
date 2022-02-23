#!/bin/bash
#web
docker build -f ./src/provisioning/web.prod.dockerfile -t docker-registry.local:5001/web-alumni:1.0.1 ./src
docker push docker-registry.local:5001/web-alumni:1.0.1
