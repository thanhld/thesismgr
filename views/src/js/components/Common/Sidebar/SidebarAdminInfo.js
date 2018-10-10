import React, { Component } from 'react'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarAdmin extends Component {
    render() {
        return (
            <ul class="nav nav-pills nav-stacked left-slide-bar">
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['DEPARTMENTS']}`}>
                        <i class="fa fa-building-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Bộ môn, PTN</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['OFFICERS']}`}>
                        <i class="fa fa-address-book-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Cán bộ</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['TRAINING_AREAS']}`}>
                        <i class="fa fa-university fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Ngành đào tạo</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['TRAINING_PROGRAMS']}`}>
                        <i class="fa fa-university fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Chương trình đào tạo</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['TRAINING_COURSES']}`}>
                        <i class="fa fa-university fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Khóa đào tạo</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['LEARNERS']}`}>
                        <i class="fa fa-graduation-cap fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Học viên</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['KNOWLEDGE_AREAS']}`}>
                        <i class="fa fa-caret-right fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Lĩnh vực nghiên cứu</span>
                    </SidebarNavLink>
                </li>
            </ul>
        )
    }
}

export default SidebarAdmin
