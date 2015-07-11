;
(function ($) {
    $(document).ready(function () {
        //var cacheTime = 1000 * 60 * 1;//1min;
        var cacheTime = 1;//dev

        var loader = function (mode) {
            $('.progress .bar').attr('class', 'bar');
            if (mode == 'show') {
                $('.progress').fadeIn().find('.bar').css('width', '100%');
            } else if (mode == 'hide') {
                $('.progress').find('.bar').addClass('bar-success').closest('.progress').fadeOut().find('.bar').css('width', 0);
            } else if (mode == 'suspend') {
                $('.progress').find('.bar').addClass('bar-warning');
            }
        }

        var RandomChars = function (strlen, opt, cut) {
            var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            if (opt) {
                switch (opt) {
                    case 'NUMBER':
                        chars = "1234567890";
                        break;
                    case 'UCASE':
                        chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                        break;
                    case 'LCASE':
                        chars = "abcdefghijklmnopqrstuvwxyz";
                        break;
                }
            }
            if (cut) {
                for (var i = cut.length - 1; i >= 0; i--) {
                    chars = chars.replace(cut[i], '');
                }
            }

            var len = chars.length;
            var result = "";
            if (!strlen) {
                strlen = Math.random(len);
            }
            var d = Date.parse(new Date());
            for (var i = 0; i < strlen; i++) {
                result += chars.charAt(Math.ceil(Math.random() * d) % len);
            }
            return  result;
        }

        var codeChecker = function (data) {
            var code = data['extra']['code'];
            var desc = data['extra']['desc'];

            if (code) {
                if (code > 99 && code < 200) {
                    return true;
                } else if (code > 199 && code < 300) {
                    return true;
                } else if (code > 299 && code < 400) {
                    return true;
                } else if (code > 399 && code < 500) {
                    return false;
                } else if (code > 499 && code < 600) {
                    return false;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        var initData = function (params) {
            var url = params.url || '?mode=admin&a=query';
            var cb = params.callback || null;
            var mode = params.mode || 'ALL';

            var initALLTable = function (data) {
                $('#table-pager').addClass('hide');
                loader('hide');
                $('#data-table tbody').empty();
                var html = '';
                var user = data.data;
                $GO$.admin = data.admin;
                $GO$.page = data.page;

                if (!codeChecker(data)) {
                    html = '<tr><td colspan="10"  class="loading-text">' + data['extra']['desc'] + '</td></tr>';
                    $('#data-table tbody').append(html);
                    $('body').data('status', 'finished');
                    if (cb) {
                        cb();
                    }
                    return false;
                } else {
                    for (var i = 0, j = user.length; i < j; i++) {
                        html += '<tr data-id="' + user[i]['id'] + '">'
                        html += '<td class="t-username">' + user[i]['username'] + '</td>';
                        html += '<td>' + user[i]['age'] + '</td>';
                        html += '<td>' + user[i]['birthday'] + '</td>';
                        html += '<td>' + (user[i]['sex'] == 1 ? '男' : '女') + '</td>';
                        html += '<td>' + (user[i]['married'] == 1 ? '未婚' : '已婚') + '</td>';
                        switch (user[i]['education']) {
                            case '1':
                                html += '<td>高中</td>';
                                break;
                            case '2':
                                html += '<td>中专</td>';
                                break;
                            case '3':
                                html += '<td>技校</td>';
                                break;
                            case '4':
                                html += '<td>大专</td>';
                                break;
                            case '5':
                                html += '<td>本科</td>';
                                break;
                            case '6':
                                html += '<td>硕士</td>';
                                break;
                            case '7':
                                html += '<td>博士</td>';
                                break;
                        }
                        switch (user[i]['job']) {
                            case '无业':
                                html += '<td>暂无工作</td>';
                                break;
                            case '无':
                                html += '<td>暂无工作</td>';
                                break;
                            case '':
                                html += '<td>暂无工作</td>';
                                break;
                            default :
                                html += '<td>' + user[i]['job'] + '</td>';
                                break
                        }
                        html += '<td>' + user[i]['address']['live'] + '</td>';
                        html += '<td>' + user[i]['email'] + '</td>';
                        html += '<td>' + user[i]['qq'] + '</td>';
                        html += '<td>' + moment(user[i]['time']).startOf('hour').fromNow() + '</td>';
                        html += '</tr>'
                    }
                }

                $('#data-table tbody').append(html);
                $('#data-table tbody tr').on('dblclick', function (e) {
                    var target = $(e.target).closest('tr');
                    var uid = target.data('id');
                    var username = target.find('.t-username').text();
                    if (uid) {
                        $('.common-modal').modal('hide').remove();
                        var modalID = RandomChars(5, 'LCASE');
                        var tpl = '<div id="' + modalID + '" class="common-modal modal hide fade" data-id="' + uid + '"></div>';
                        $('body').append(tpl);
                        $('#' + modalID).html($('#common-modal-tpl').html()).find('.modal-header h3').text(username + ' :数据加载中...');
                        $('body').find('.common-modal').addClass('script-modal');
                        var tpl = '<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>';
                        $('#' + modalID).find('.modal-body').html(tpl);
                        $('#' + modalID).modal('show');
                        var callbackModal = $('#' + modalID);
                        var minData = null;

                        for (var aa in user) {
                            if (uid == user[aa]['id']) {
                                minData = user[aa];
                            }
                        }

                        $.ajax({
                            'url': '?mode=admin&a=user&id=' + uid,
                            'success': function (resp) {
                                var bigData = resp;
                                var html = '<h4>基本资料</h4>';
                                html += '<table class="table table-bordered person-table"><tbody>';
                                html += '<tr><th>姓名</th><td>' + minData['username'] + '</td><th>性别</th><td>' + (minData['sex'] == 1 ? '男' : '女') + '</td></tr>';
                                html += '<tr><th>生日</th><td>' + minData['birthday'] + '</td><th>年龄</th><td>' + minData['age'] + '</td></tr>';
                                html += '<tr><th>婚姻</th><td>' + (minData['married'] == 1 ? '未婚' : '已婚') + '</td><th>教育程度</th>';
                                switch (minData['education']) {
                                    case '1':
                                        html += '<td>高中</td>';
                                        break;
                                    case '2':
                                        html += '<td>中专</td>';
                                        break;
                                    case '3':
                                        html += '<td>技校</td>';
                                        break;
                                    case '4':
                                        html += '<td>大专</td>';
                                        break;
                                    case '5':
                                        html += '<td>本科</td>';
                                        break;
                                    case '6':
                                        html += '<td>硕士</td>';
                                        break;
                                    case '7':
                                        html += '<td>博士</td>';
                                        break;
                                }
                                html += '</tr>';
                                html += '<tr><th>现居住地</th><td>' + minData['address']['live'] + '</td><th>职业</th>';
                                switch (minData['job']) {
                                    case '无业':
                                        html += '<td colspan="3">暂无工作</td>';
                                        break;
                                    case '无':
                                        html += '<td colspan="3">暂无工作</td>';
                                        break;
                                    case '':
                                        html += '<td colspan="3">暂无工作</td>';
                                        break;
                                    default :
                                        html += '<td colspan="3">' + minData['job'] + '</td>';
                                        break
                                }
                                html += '</tr>';
                                html += '<tr><th>籍贯</th><td>' + minData['address']['hometown'] + '</td><th>身份证号</th><td>'+minData['id_num']+'</td></tr>';
                                html += '</tbody></table>';

                                if (codeChecker(resp)) {
                                    html += '<h4>受教育经历</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    html += '<tr><th>中学</th><td>' + bigData['data']['education']['high'] + '</td></tr>';
                                    html += '<tr><th>大学</th><td>' + bigData['data']['education']['university'] + '</td></tr>';
                                    html += '</tbody></table>';
                                }
                                if (codeChecker(resp) && (bigData['data']['photo']['id'] || bigData['data']['photo']['edu'] || bigData['data']['photo']['user')]) {
                                    html += '<h4>照片</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    if(bigData['data']['photo']['id'])
                                      html += '<tr><th>身份证</th><td><img src="aef-upload/user_photo/' + bigData['data']['photo']['id'] + '"></td></tr>';
                                    if(bigData['data']['photo']['edu'])
                                      html += '<tr><th>学历</th><td><img src="aef-upload/user_photo/' + bigData['data']['photo']['edu'] + '"></td></tr>';
                                    if(bigData['data']['photo']['user'])
                                    	html += '<tr><th>生活</th><td><img src="aef-upload/user_photo/' + bigData['data']['photo']['user'] + '"></td></tr>';
                                    html += '</tbody></table>';
                                }
                                if (codeChecker(resp)) {
                                    html += '<h4>工作经历</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    html += '<tr><th>工作经历</th><td>' + bigData['data']['work'] + '</td></tr>';
                                    html += '</tbody></table>';
                                }

                                html += '<h4>联系方式</h4>'
                                html += '<table class="table table-bordered person-table"><tbody>';
                                html += '<tr><th>邮政地址</th><td>' + minData['address']['post_addr'] + '</td></tr>';
                                html += '<tr><th>邮政编码</th><td>' + minData['address']['post_code'] + '</td></tr>';
                                html += '<tr><th>联系电话</th><td>' + minData['phone'] + '</td></tr>';
                                html += '<tr><th>电子邮件</th><td>' + minData['email'] + '</td></tr>';
                                html += '<tr><th>QQ/微信</th><td>' + minData['qq'] + '</td></tr>';
                                html += '</tbody></table>';

                                if (codeChecker(resp)) {
                                    html += '<h4>家庭成员</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    html += '<tr><th>称谓</th><td>' + bigData['data']['family']['title'] + '</td><th>姓名</th><td>' + bigData['data']['family']['name'] + '</td></tr>';
                                    html += '<tr><th>联系方式</th><td colspan="3">' + bigData['data']['family']['contact'] + '</td></tr>';
                                    html += '<tr><th>工作单位</th><td colspan="3">' + bigData['data']['family']['workplace'] + '</td></tr>';
                                    html += '</tbody></table>';
                                }

                                if (codeChecker(resp) && bigData['data']['urgent']['title']) {
                                    html += '<h4>紧急联络人</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    html += '<tr><th>称谓</th><td>' + bigData['data']['urgent']['title'] + '</td><th>姓名</th><td>' + bigData['data']['urgent']['name'] + '</td></tr>';
                                    html += '<tr><th>联系方式</th><td colspan="3">' + bigData['data']['urgent']['contact'] + '</td></tr>';
                                    html += '<tr><th>工作单位</th><td colspan="3">' + bigData['data']['urgent']['workplace'] + '</td></tr>';
                                    html += '</tbody></table>';
                                }

                                if (codeChecker(resp) && bigData['data']['experience']) {
                                    html += '<h4>个人特别技能及资历</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    html += '<tr><td>' + bigData['data']['experience'] + '</td></tr>';
                                    html += '</tbody></table>';
                                }

                                if (codeChecker(resp)) {
                                    html += '<h4>其它资料</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    html += '<tr><th>有否伤残病历</th><td>' + bigData['data']['disability'] + '</td></tr>';
                                    html += '<tr><th>有否支教经验</th><td colspan="3">' + bigData['data']['experience'] + '</td></tr>';
                                    html += '<tr><th>预计支教期限</th><td colspan="3">' + bigData['data']['date']['predict'] + '</td></tr>';
                                    html += '<tr><th>愿意开始支教的日期</th><td colspan="3">' + bigData['data']['date']['begin'] + '</td></tr>';
                                    html += '<tr><th>何处得知本活动</th><td colspan="3">' + bigData['data']['form'] + '</td></tr>';
                                    html += '</tbody></table>';
                                }
                                if (codeChecker(resp)) {
                                    html += '<h4>考察问题</h4>'
                                    html += '<table class="table table-bordered person-table"><tbody>';
                                    bigData['questions'] = [
                                    	  '1、请简述您为什么要支教？您对支教有哪些了解？希望通过支教收获什么？',
                                    	  '2、请简述对您影响最大的一位老师是如何教学的？您认为作为一个老师应该具备哪些品质？又该如何去做一名合格的老师？',
                                    	  '3、请简述您的家庭教育中有哪些值得学习的经验和不足，孩童时期对你影响最大的一件事是什么？为什么？',
                                    	  '4、请简述未来五年内的人生规划？',
                                        '您目前的工作或学习是否属在职？',
                                        '简述您的经济来源与状况，并权衡是否可支付支教过程中的基本生活费、路费的同时还能保留适当的备用金。',
                                        '您何时有计划支教的想法？该想法是否与您周边的亲朋好友进行过沟通？他们对您计划支教的想法所持的态度如何？',
                                        '请告知您的直系长辈亲属的联系方式，以便我们与您的家人联系，获取他们对您计划支教的支持程度。',
                                        '简述您为什么要做支教？请列出您参与支教的价值及意义。',
                                        '您是否曾经（或者最近）加入过其他公益组织，做过什么志愿服务，请略述之。（包括公益组织名称、服务内容、自己的观点等。）您在志愿服务中获得了哪些难忘的经验？',
                                        '您是否清楚支教所存在的风险，认为支教前应做哪些准备工作？',
                                        '您是否愿意提供近三个月内的体检报告以便我们对您的健康状况进行简单的评估。',
                                        '对于参与该项志愿服务，您对自己有何期望或希望贡献什么？',
                                        '您对支教的期望',
                                        '其他意见'
                                    ];
                                    for (var iii = 0, jjj = 11; iii < jjj; iii++) {
                                    	  if (bigData['data']['question'][iii]) {
	                                        html += '<tr><th>' + bigData['questions'][iii] + '</th></tr>';
	                                        html += '<tr><td>' + bigData['data']['question'][iii] + '</td></tr>';
                                        }
                                    }
                                    html += '</tbody></table>';
                                }

                                html += '<a class="btn btn-primary" href="#CMD:USER-ACCEPT">审核通过</a>';
                                html += '<a class="btn btn-danger user-forbidden" href="#CMD:USER-FORBIDDEN">审核拒绝</a>';

                                callbackModal.animate({'left': '2%', 'margin-left': '0', 'width': '96%'}, 'slow', 'swing', function () {
                                    callbackModal.find('.progress').fadeOut().remove();
                                    callbackModal.find('.modal-body').append(html);
                                    callbackModal.find('.modal-header h3').text(minData['username']);
                                });
                            }
                        });
                    }
                })
                $('body').data('status', 'finished');
                if (cb) {
                    cb();
                }
            }

            var initLOGTable = function(data){
                $('#table-pager').addClass('hide');
                loader('hide');
                $('#data-table tbody').empty();
                var html = '';
                var log = data.data;
                $GO$.admin = data.admin;
                $GO$.page = data.page;

                if (!codeChecker(data)) {
                    html = '<tr><td colspan="10"  class="loading-text">' + data['extra']['desc'] + '</td></tr>';
                    $('#data-table tbody').append(html);
                    $('body').data('status', 'finished');
                    if (cb) {
                        cb();
                    }
                    return false;
                } else {
                    for (var i = 0, j = log.length; i < j; i++) {
                        html += '<tr">'
                        html += '<td>' + log[i]['content'] + '</td>';
                        html += '<td>' + moment(log[i]['date']).startOf('hour').fromNow() + '</td>';
                        html += '</tr>'
                    }
                }

                $('#data-table tbody').append(html);
                $('body').data('status', 'finished');
                if (cb) {
                    cb();
                }

            }
            var userData = $.jStorage.get('GO');
            if (userData) {
                switch (mode) {
                    case 'ALL':
                        initALLTable(userData);
                        break;
                    case 'UNAUDITED':
                        initALLTable(userData);
                        break;
                    case 'AUDITED':
                        initALLTable(userData);
                        break;
                    case 'FORBIDDEN':
                        initALLTable(userData);
                        break;
                    case 'VIEW-LOG':
                        initLOGTable(userData);
                        break;
                }
                return false;
            }

            $.ajax({url: url, dataType: 'json', type: 'GET', success: function (data) {
                $.jStorage.set('GO', data, {TTL: cacheTime});
                switch (mode) {
                    case 'ALL':
                        initALLTable(data);
                        break;
                    case 'UNAUDITED':
                        initALLTable(data);
                        break;
                    case 'AUDITED':
                        initALLTable(data);
                        break;
                    case 'FORBIDDEN':
                        initALLTable(data);
                        break;
                    case 'VIEW-LOG':
                        initLOGTable(data);
                        break;
                }
                if ($GO$['page'] && $GO$['page']['total'] > 1) {
                    $('#table-pager').removeClass('hide');
                    var html = '';
                    html += '<ul><li class="page-prev"><a href="#CMD:LoadPage" data-pid="prev">Prev</a></li>';
                    html += '<li class="page-item' + ($GO$['page']['cur'] == 1 ? ' active' : '') + '"><a href="#CMD:LoadPage" data-pid="1">1</a></li>';
                    if ($GO$['page']['total'] > 2) {
                        for (var ii = 2, jj = $GO$['page']['total']; ii < jj; ii++) {
                            if ((ii <= 3) || ((ii >= jj - 2) && (ii != 3)) || (ii == $GO$['page']['cur'])) {
                                html += '<li class="page-item' + ($GO$['page']['cur'] == ii ? ' active' : '') + '"><a href="#CMD:LoadPage" data-pid="' + ii + '">' + ii + '</a></li>';
                            } else if (((ii + 1 == $GO$['page']['cur']) && (ii > 3)) || ((ii - 1 == $GO$['page']['cur']) && (ii < jj - 2))) {
                                html += '<li class="page-item disabled"><a href="#CMD:LoadPage" data-pid="NO">...</a></li>';
                            }
                        }
                    }
                    html += '<li class="page-item' + ($GO$['page']['cur'] == $GO$['page']['total'] ? ' active' : '') + '"><a href="#CMD:LoadPage" data-pid="' + $GO$['page']['total'] + '">' + $GO$['page']['total'] + '</a></li>';
                    html += '<li class="page-next"><a href="#CMD:LoadPage" data-pid="next">Next</a></li></ul>';
                    $('#table-pager').html(html);
                } else {
                    $('#table-pager').addClass('hide');
                }
            },
                beforeSend: function () {
                    $('#table-pager').addClass('hide');
                    if ($('body').data('status') == 'loading') {
                        $('.loading-text').text('等待请求结束 ...');
                        loader('suspend');
                        return false;
                    } else {
                        $('body').data('status', 'loading');
                        $('.loading-text').text('数据正在加载中 ...');
                        loader('show');
                    }
                }
            });
        }

        //init
        $('#data-table').empty().html($('#table-all-tpl').html());
        initData({'mode': 'ALL'});

        var initBtns = function () {
            var initNavBtn = function (target) {
                $('#control-nav').find('li.active').removeClass('active');
                target.closest('li').addClass('active');
            }
            var body = $('body');
            body.on('click', function (e) {
                var target = $(e.target);
                if (target.closest('a[href*=#CMD]')) {
                    e.preventDefault();
                    var cmd = target.attr('href');
                    if (cmd) {
                        cmd = cmd.split('#CMD:')[1];
                        switch (cmd) {
                            case 'ALL':
                                $GO$.mode = 'user';
                                initNavBtn(target);
                                $('#data-table').empty().html($('#table-all-tpl').html());
                                initData({'mode': 'ALL'});
                                break;
                            case 'UNAUDITED':
                                $GO$.mode = 'user';
                                initNavBtn(target);
                                $('#data-table').empty().html($('#table-all-tpl').html());
                                initData({'mode': 'UNAUDITED', 'url': '?mode=admin&a=query&type=2'});
                                break;
                            case 'AUDITED':
                                $GO$.mode = 'user';
                                initNavBtn(target);
                                $('#data-table').empty().html($('#table-all-tpl').html());
                                initData({'mode': 'AUDITED', 'url': '?mode=admin&a=query&type=3'});
                                break;
                            case 'FORBIDDEN':
                                initNavBtn(target);
                                $('#data-table').empty().html($('#table-all-tpl').html());
                                initData({'mode': 'FORBIDDEN', 'url': '?mode=admin&a=query&type=4'});
                                break;
                            case 'LoadPage':
                                var action = target.data('pid');
                                var curPage = parseInt($GO$['page']['cur']);
                                var lastPage = parseInt($GO$['page']['total']);
                                var pageType = parseInt($GO$['page']['type']);
                                switch (action) {
                                    case 'prev':
                                        if (curPage != 1) {
                                            curPage -= 1;
                                        }
                                        else {
                                            return false;
                                        }
                                        break;
                                    case 'next':
                                        if (curPage != lastPage) {
                                            curPage += 1;
                                        }
                                        else {
                                            return false;
                                        }
                                        break;
                                    case 'NO':
                                        return false;
                                        break;
                                    default :
                                        if (curPage == action) {
                                            return false;
                                        } else {
                                            curPage = action;
                                        }
                                        break;
                                }
                                var mode = $GO$.mode;
                                var url = null;
                                switch (mode){
                                    case 'user':
                                        initData({'url': '?mode=admin&a=query&page=' + curPage + '&type=' + pageType, 'mode': 'ALL'});
                                        break;
                                    case 'log':
                                        initData({'url': '?mode=admin&a=view-log&page=' + curPage, 'mode': 'VIEW-LOG'});
                                        break;
                                    default :
                                        initData({'url': '?mode=admin&a=query&page=' + curPage + '&type=' + pageType, 'mode': 'ALL'});
                                        break;
                                }
                                break;
                            case 'USER-ACCEPT':
                                var id = target.closest('.common-modal').data('id');
                                $.ajax({
                                    dataType: "json",
                                    url: '?mode=admin&a=user-accept&id=' + id,
                                    data: null,
                                    success: function (resp) {
                                        initData({'mode': 'FORBIDDEN', 'url': '?mode=admin&a=query&type='+$GO$['page']['type']});
                                        $('.common-modal').modal('hide').remove();
                                    }
                                })
                                break;
                            case 'USER-FORBIDDEN':
                                var id = target.closest('.common-modal').data('id');
                                $.ajax({
                                    dataType: "json",
                                    url: '?mode=admin&a=user-forbidden&id=' + id,
                                    data: null,
                                    success: function (resp) {
                                        initData({'mode': 'FORBIDDEN', 'url': '?mode=admin&a=query&type='+$GO$['page']['type']});
                                        $('.common-modal').modal('hide').remove();
                                    }
                                })
                                break;
                            case 'VIEW-LOG':
                                initNavBtn(target);
                                $GO$.mode = 'log';
                                $('#data-table').empty().html($('#table-log-tpl').html());
                                initData({'mode': 'VIEW-LOG', 'url': '?mode=admin&a=view-log'});
                                break;
                            case 'FLITER':
                                console.log('1')
                                break;
                        }
                    }
                }
            })
        }
        initBtns();
    });
})(jQuery, "http://soulteary.com")

