import React, { Component } from 'react'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarSuperuser extends Component {
    render() {
        return (
            <ul class="nav nav-pills nav-stacked left-slide-bar">
                {/* <li class="text-muted menu-title">Khu vực của Superuser</li> */}
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['SUPERUSER']}/${routeName['FACULTIES']}`}>
                        <i class="fa fa-graduation-cap fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Quản lý Khoa </span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar">
                    <a role="button" data-toggle="collapse" href="#categories" aria-expanded="false" aria-controls="topics">
                        <i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Danh mục chung
                            <i class="pull-right fa fa-caret-down" aria-hidden="true"></i>
                        </span>
                    </a>
                </li>
                <ul id="categories" class="collapse nav nav-pills nav-stacked ul-sub-topics">
                    <li class="li-left-slidebar" onClick={this.props.closebar}>
                        <SidebarNavLink to={`/${routeName['SUPERUSER']}/${routeName['TRAININGS']}`}>
                            <i class="fa fa-caret-right fa-lg fa-fw" aria-hidden="true"></i>
                            <span> Hệ đào tạo, Bậc đào tạo</span>
                        </SidebarNavLink>
                    </li>
                    <li class="li-left-slidebar" onClick={this.props.closebar}>
                        <SidebarNavLink to={`/${routeName['SUPERUSER']}/${routeName['DEGREES']}`}>
                            <i class="fa fa-caret-right fa-lg fa-fw" aria-hidden="true"></i>
                            <span> Học hàm, học vị</span>
                        </SidebarNavLink>
                    </li>
                    <li class="li-left-slidebar" onClick={this.props.closebar}>
                        <SidebarNavLink to={`/${routeName['SUPERUSER']}/${routeName['QUOTAS']}`}>
                            <i class="fa fa-caret-right fa-lg fa-fw" aria-hidden="true"></i>
                            <span> Định mức, hệ số HD</span>
                        </SidebarNavLink>
                    </li>
                </ul>
            </ul>
        )
    }
}

export default SidebarSuperuser
