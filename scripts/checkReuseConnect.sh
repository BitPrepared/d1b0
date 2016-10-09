#!/bin/bash
#

reuse=$(curl -vk https://www.d1b0.local:8443/workspace/index.php https://www.d1b0.local:8443/workspace/index.php | grep "Re-using existing connection" | wc -l)

if [ $reuse -ne 0 ]; then
  exit 2
fi
