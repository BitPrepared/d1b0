#!/bin/bash

#mermaid assets/diagrams/smartpenflow.mmd --sequenceConfig assets/diagrams/sequence.config --outputDir assets/images/
#
#
mermaid registrationFacebook.mmd --sequenceConfig sequence.config --outputDir img/
mermaid loginFacebook.mmd --sequenceConfig sequence.config --outputDir img/
mermaid loginMail.mmd --sequenceConfig sequence.config --outputDir img/
mermaid registration.mmd --sequenceConfig sequence.config --outputDir img/
mermaid creatingWorkspace.mmd --sequenceConfig sequence.config --outputDir img/
mermaid creatingPart.mmd --sequenceConfig sequence.config --outputDir img/
mermaid shareWorkspace.mmd --sequenceConfig sequence.config --outputDir img/
mermaid getWorkspace.mmd --sequenceConfig sequence.config --outputDir img/
mermaid ticket.mmd --sequenceConfig sequence.config --outputDir img/
