import React, { Component } from 'react'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarPublic extends Component {
    render() {
        return (
            <ul class="nav nav-pills nav-stacked left-slide-bar">
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to="/">
                        <i class="fa fa-home fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Trang chủ </span>
                    </SidebarNavLink>
                </li>
            </ul>
        )
    }
}

export default SidebarPublic
