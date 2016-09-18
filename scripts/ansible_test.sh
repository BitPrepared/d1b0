#!/bin/bash
cd server
ansible-playbook -i dev.hosts  site.yml --check
