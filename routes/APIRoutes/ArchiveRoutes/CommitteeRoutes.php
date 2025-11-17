<?php

use App\Http\Controllers\Committee\CommitteeController;
use App\Http\Controllers\Committee\CommitteeMemberController;
use App\Http\Controllers\User\RoleController;

use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('/committees')->group(function () {

    Route::prefix('/members')->group(function () {
        Route::middleware(Accessibility::class . ':committee_members,create')->post('/', [CommitteeMemberController::class, 'store']);
        Route::middleware(Accessibility::class . ':committee_members,read')->get('/', [CommitteeMemberController::class, 'index']);
        Route::middleware(Accessibility::class . ':committee_members,read')->get('/count', [CommitteeMemberController::class, 'getMembersCount']);
        Route::middleware(Accessibility::class . ':committee_members,edit')->put('/{memberId}', [CommitteeMemberController::class, 'update']);
        Route::middleware(Accessibility::class . ':committee_members,edit')->patch('{memberId}/change-committee-id', [CommitteeMemberController::class, 'changeCommitteeId']);
        Route::middleware(Accessibility::class . ':committee_members,read')->get('/list', [CommitteeMemberController::class, 'listOfAllMembers']);
    });

    Route::middleware(Accessibility::class . ':committee,create')->post('/', [CommitteeController::class, 'store']);
    Route::middleware(Accessibility::class . ':committee,read')->get('/', [CommitteeController::class, 'index']);
    Route::middleware(Accessibility::class . ':committee,read')->get('/list', [CommitteeController::class, 'listOfAllCommittees']);
    Route::middleware(Accessibility::class . ':committee,read')->get('/count', [CommitteeController::class, 'getCommitteesCount']);
    Route::put('/update-personal-password', [CommitteeController::class, 'updatePersonalPassword']);
    Route::prefix('/{committeeId}')->group(function () {
        Route::get('/', [CommitteeController::class, 'show']);
        Route::middleware(Accessibility::class . ':committee,editIsCurrent')->put('/setIsCurrent', [CommitteeController::class, 'setIsCurrent']);
        Route::middleware(['auth:sanctum'])->put('/update-personal-password', [CommitteeController::class, 'updatePersonalPassword']);
    });
    Route::post('/login', [CommitteeController::class, 'login']);


    Route::middleware(Accessibility::class . ':roles,read')->get('/roles', [RoleController::class, 'index']);
});
