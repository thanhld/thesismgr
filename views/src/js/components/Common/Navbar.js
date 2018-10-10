import React, { Component } from 'react'
import { Link } from 'react-router'
import { browserHistory } from 'react-router'
import { isOfficerAdmin } from 'Helper'

class Navbar extends Component {
    constructor() {
        super()
        this.handleLogout = this.handleLogout.bind(this)
    }
    handleLogout() {
        const { actions } = this.props
        actions.logout().then(() => {
            browserHistory.push('/login')
        }).catch(err => {
            console.log("Đăng xuất không thành công");
        })
    }
    render() {
        const { user } = this.props
        return (
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header-title">
                        <Link to="/" class="navbar-header-title__brand">
                            ThesisMgr
                        </Link>
                    </div>
                    { user &&
                        <div class="btn-group pull-right navbar-header-user-menu">

                            <span class="drop-down">
                                <div class="label-username clickable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true">
                                    <i class="fa fa-user" aria-hidden="true" ></i>
                                    {user.username}
                                </div>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <Link to='/set-password'>
                                            <i class="fa fa-lock fa-fw" aria-hidden="true"></i>
                                            <span> Đổi mật khẩu</span>
                                        </Link>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:;" onClick={this.handleLogout}><i class="fa fa-sign-out fa-fw" aria-hidden="true"></i> Đăng xuất</a></li>
                                </ul>
                            </span>
                        </div> }
                </div>
            </nav>
        )
    }
}

export default Navbar
