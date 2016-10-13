#!/bin/bash
#

reuse=$(curl -vk https://dev.d1b0.local:8443/workspace/index.php https://dev.d1b0.local:8443/workspace/index.php | grep "Re-using existing connection" | wc -l)

if [ $reuse -ne 0 ]; then
  exit 2
fi
