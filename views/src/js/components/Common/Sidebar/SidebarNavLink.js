import React, { Component } from 'react'
import { Link } from 'react-router'

class SidebarNavLink extends Component {
    render() {
        return (
            <Link {...this.props} activeClassName="active" onlyActiveOnIndex={true} />
        )
    }
}

export default SidebarNavLink
