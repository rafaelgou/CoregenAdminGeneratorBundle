<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('{{ route_name_prefix }}', new Route('/', array(
    '_controller' => '{{ bundle }}:{{ entity }}:index',
)));

$collection->add('{{ route_name_prefix }}_show', new Route('/{id}/show', array(
    '_controller' => '{{ bundle }}:{{ entity }}:show',
)));

$collection->add('{{ route_name_prefix }}_new', new Route('/new', array(
    '_controller' => '{{ bundle }}:{{ entity }}:new',
)));

$collection->add('{{ route_name_prefix }}_create', new Route(
    '/create',
    array('_controller' => '{{ bundle }}:{{ entity }}:create'),
    array('_method' => 'post')
));

$collection->add('{{ route_name_prefix }}_edit', new Route('/{id}/edit', array(
    '_controller' => '{{ bundle }}:{{ entity }}:edit',
)));

$collection->add('{{ route_name_prefix }}_update', new Route(
    '/{id}/update',
    array('_controller' => '{{ bundle }}:{{ entity }}:update'),
    array('_method' => 'post')
));

$collection->add('{{ route_name_prefix }}_delete', new Route(
    '/{id}/delete',
    array('_controller' => '{{ bundle }}:{{ entity }}:delete'),
    array('_method' => 'post')
));

$collection->add('{{ route_name_prefix }}_batch_actions', new Route(
    '/batch_actions',
    array('_controller' => '{{ bundle }}:{{ entity }}:batch_actions'),
    array('_method' => 'post')
));

return $collection;
