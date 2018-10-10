import React, { Component } from 'react'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarDepartment extends Component {
    render() {
        return <ul class="nav nav-pills nav-stacked left-slide-bar">
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['DEPARTMENT']}/${routeName['TOPICS']}`}>
                        <i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Thẩm định đề cương</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['DEPARTMENT']}/${routeName['SEMINAR']}`}>
                        <i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Kiểm tra tiến độ</span>
                    </SidebarNavLink>
                </li>
        </ul>
    }
}

export default SidebarDepartment
