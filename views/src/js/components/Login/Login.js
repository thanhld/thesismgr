import React, { Component } from 'react'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { Navbar } from 'Components'
import * as config from 'Config'
import ForgotPassword from '../Login/ForgotPassword'

class Login extends Component {
    constructor() {
        super()
        this.state = {
            username: '',
            password: '',
            error: false,
            message: '',
            annoucements: []
        }
        this.handleSubmit = this.handleSubmit.bind(this)
    }
    componentWillMount() {
        // axios.get('/announcement').then(res => {
        //     console.log(res);
        // })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { actions } = this.props
        const { username, password } = this.state
        // Authenticate
        actions.auth(username, password).then((res) => {
            const { role } = res.value.data;
            if ( role == 0 ) browserHistory.push('/superuser/faculties')
            else browserHistory.push('/')
            // else browserHistory.goBack()
        }).catch((err) => {
            this.setState({
                error: true,
                message: "Sai tên đăng nhập hoặc mật khẩu."
            })
        })
    }
    render() {
        const { username, password, error, message } = this.state
        return (
            <div>
                <Navbar />

                <div class="login-page-container">
                    <div class="col-md-8">
                        <div class="container">
                            <h2>Thông báo</h2>
                            <p>Chưa có thông báo nào.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="login-box">
                            <form onSubmit={this.handleSubmit}>
                                <div class="login-title">
                                    ĐĂNG NHẬP
                                </div>
                                <hr />
                                <br />
                                <div class="form-group">
                                    <label for="username">Tên đăng nhập</label>
                                    <input id="username" type="text" placeholder="Tên đăng nhập" class="form-control" value={username} autoFocus onChange={(e) => this.setState({username: e.target.value})} />
                                </div>
                                <div class="form-group">
                                    <label for="password">Mật khẩu</label>
                                    <input id="password" type="password" placeholder="Mật khẩu" class="form-control" value={password} onChange={(e) => this.setState({password: e.target.value})} />
                                </div>
                                <hr />
                                <br />
                                { error && <div class="alert alert-danger" role="alert">{message}</div> }
                                <div>
                                    <a class="forget-password-link" data-toggle="modal" data-target="#forgotPassword">Quên mật khẩu?</a>
                                    <button type="submit" class="btn btn-success pull-right">
                                        <i class="fa fa-sign-in" aria-hidden="true"></i> Đăng nhập
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <ForgotPassword
                    actions={this.props.actions}
                />
            </div>
        )
    }
}

export default Login
