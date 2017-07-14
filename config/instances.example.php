<?php

/**
 * This file configures instances and the number of
 * accounts each instance needs to run with.
 *
 * `names` is the number of unique names in the batch,
 * `total` is the number of accounts to create.
 *
 * In the below example, 5 unique prefixes will be generated,
 * with each prefix used 20 times to create 100 unique accounts.
 */

return [
    'default' => [
        'names' => 5,
        'total' => 100,
    ],
];
