import React, { Component } from 'react'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarAdmin extends Component {
    render() {
        const { topics } = this.props
        const numRequest = topics.list.length
        return (
            <ul class="nav nav-pills nav-stacked left-slide-bar">
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['TOPICS']}/${routeName['ADMIN_TOPIC_STUDENT']}`}>
                        <i class="fa fa-bookmark-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Khóa luận tốt nghiệp</span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['TOPICS']}/${routeName['TOPIC_OUT_OFFICERS']}`}>
                        <i class="fa fa-check-square-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Xác nhận GV
                            <span class="pull-right">
                                {numRequest > 0 && <div class="badge badge-danger badge-margin">{numRequest}</div>}
                            </span>
                        </span>
                    </SidebarNavLink>
                </li>
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['ADMIN']}/${routeName['DOCUMENTS']}`}>
                        <i class="fa fa-file-text-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span>  Tờ trình, quyết định</span>
                    </SidebarNavLink>
                </li>
            </ul>
        )
    }
}

export default SidebarAdmin
