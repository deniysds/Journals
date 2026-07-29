<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Main Journals Breadcrumb
Breadcrumbs::for('journals', function (BreadcrumbTrail $trail) {
    $trail->push(__('journals::app.journals'), route('journals.index'));
});

// Create Journal
Breadcrumbs::for('journals.create', function (BreadcrumbTrail $trail) {
    $trail->parent('journals');
    $trail->push(__('journals::app.add_journal'), route('journals.create'));
});

// Show Journal
Breadcrumbs::for('journals.show', function (BreadcrumbTrail $trail, $journal) {
    $trail->parent('journals');
    $name = is_object($journal) ? $journal->name : 'Detail Journal';
    $trail->push($name, route('journals.show', $journal));
});

// Edit Journal
Breadcrumbs::for('journals.edit', function (BreadcrumbTrail $trail, $journal) {
    $trail->parent('journals');
    $trail->push(__('journals::app.edit_journal'));
});
