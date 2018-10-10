import React, { Component } from 'react'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarLearner extends Component {
    render() {
        const { user } = this.props;
        return (
            <ul class="nav nav-pills nav-stacked left-slide-bar">
                {/* <li class="text-muted menu-title">Khu vực của Người học </li> */}
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['LEARNER']}/${routeName['LEARNER_PROFILE']}`}>
                        <i class="fa fa-id-badge fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Trang cá nhân </span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['BROWSE_LECTURERS']}`}>
                        <i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Tìm kiếm giảng viên </span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['LEARNER']}/${routeName['LEARNER_TOPIC']}`}>
                        <i class="fa fa-bookmark-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Đề tài của tôi </span>
                    </SidebarNavLink>
                </li>
            </ul>
        )
    }
}

export default SidebarLearner
