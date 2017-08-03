#!/bin/bash

# TODO: Convert update.php to a command so we can ditch this script completely.

php pogomap service restart ns -i $1
