<?php

use App\Http\Controllers\Committee\CommitteeController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Committee\CommitteeMemberController;

Route::prefix('committees')->group(function () {

    Route::prefix('/members')->group(function () {

        Route::middleware(Accessibility::class . ':committee_members,create')->post('/', [CommitteeMemberController::class, 'store']);

        Route::middleware(Accessibility::class . ':committee_members,read')->get('/', [CommitteeMemberController::class, 'index']);

        Route::middleware(Accessibility::class . ':committee_members,read')->get('/count', [CommitteeMemberController::class, 'getMembersCount']);

        Route::middleware(Accessibility::class . ':committee_members,read')->get('/list', [CommitteeMemberController::class, 'listOfAllMembers']);

        Route::prefix('/{memberId}')->group(function () {

            Route::middleware(Accessibility::class . ':committee_members,edit')->get('/', [CommitteeMemberController::class, 'show']);
            Route::middleware(Accessibility::class . ':committee_members,edit')->put('/', [CommitteeMemberController::class, 'update']);

            Route::middleware(Accessibility::class . ':committee_members,edit')->patch('/change-committee-id', [CommitteeMemberController::class, 'changeCommitteeId']);
        });
    });

    Route::middleware(Accessibility::class . ':committee,create')->post('/', [CommitteeController::class, 'store']);

    Route::middleware(Accessibility::class . ':committee,read')->get('/', [CommitteeController::class, 'index']);

    Route::middleware(Accessibility::class . ':committee,read')->get('/list', [CommitteeController::class, 'listOfAllCommittees']);

    Route::middleware(Accessibility::class . ':committee,read')->get('/count', [CommitteeController::class, 'getCommitteesCount']);

    Route::prefix('/{committeeId}')->group(function () {
        Route::middleware(Accessibility::class . ':committee,editIsCurrent')->put('/setIsCurrent', [CommitteeController::class, 'setIsCurrent']);
    });
});
