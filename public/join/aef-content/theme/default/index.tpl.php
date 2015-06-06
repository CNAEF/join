<?php if (!defined('FILE_PREFIX')) include '../../../error-forbidden.php'; ?>
<div class="progress progress-striped active hide" id="page-loader">
    <div class="bar" style="width: 0;"></div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <h1 id="logo" class="pull-right">志愿者管理后台</h1>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <ul class="nav nav-tabs" id="control-nav">
                <li class="active"><a href="#CMD:ALL">全部</a></li>
                <li><a href="#CMD:UNAUDITED">未审核</a></li>
                <li><a href="#CMD:AUDITED">已审核</a></li>
                <li><a href="#CMD:FORBIDDEN">已拒绝</a></li>
                <li><a href="#CMD:VIEW-LOG">管理记录</a></li>
                <li><a href="#CMD:FLITER">搜索记录</a></li>
                <li class="hide"><a href="#CMD:ANALYSIS">统计报告</a></li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <table class="table table-striped table-hover" id="data-table"></table>
        <div class="pagination hide" id="table-pager"></div>
    </div>
</div>
