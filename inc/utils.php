<?php

function retornarUrl() {
    global $wp;
    return home_url($wp->request);
}