<?php

function formatTotalAmount($amount)
{
    return number_format($amount, 2, '.', '');
}