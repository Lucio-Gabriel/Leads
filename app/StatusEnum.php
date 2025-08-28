<?php

namespace App;

enum StatusEnum: string
{
    case novo = 'novo';
    case contato_realizado = 'contato_realizado';
    case em_negociacao = 'em_negociacao';
    case convertido = 'convertido';
}
