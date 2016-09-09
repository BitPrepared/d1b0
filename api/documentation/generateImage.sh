#!/bin/bash

#mermaid assets/diagrams/smartpenflow.mmd --sequenceConfig assets/diagrams/sequence.config --outputDir assets/images/
#
#
mermaid registrationFacebook.mmd --sequenceConfig sequence.config
mermaid loginFacebook.mmd --sequenceConfig sequence.config
mermaid loginMail.mmd --sequenceConfig sequence.config
mermaid registration.mmd --sequenceConfig sequence.config
mermaid creatingWorkspace.mmd --sequenceConfig sequence.config
mermaid creatingPart.mmd --sequenceConfig sequence.config
mermaid shareWorkspace.mmd --sequenceConfig sequence.config
mermaid getWorkspace.mmd --sequenceConfig sequence.config
