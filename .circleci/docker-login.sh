#!/bin/bash

echo "$CI_REGISTRY_TOKEN" | docker login -u "$CI_REGISTRY_USER" --password-stdin $CI_REGISTRY
