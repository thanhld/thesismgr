import React, { Component } from 'react'
import { Navbar } from 'Components'
import { browserHistory } from 'react-router'
import { Link } from 'react-router'

class SetPassword extends Component {
    constructor() {
        super();
        this.state = {
            oldpassword: '',
            newpassword: '',
            password: '',
            error: false,
            message: ''
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.refreshForm = this.refreshForm.bind(this);
    }

    refreshForm() {
        this.setState ({
            oldpassword: '',
            newpassword: '',
            password: '',
            error: false,
            message: ''
        })
    }

    handleSubmit(e) {
        e.preventDefault();
        const { uid, token } = this.props.params;
        const { actions } = this.props;
        const setPassword = this;
        const { oldpassword, newpassword, password } = this.state;

        const compare = newpassword.localeCompare(password);

        if(compare === 0) {
            //CHAGE PASSWORD case, 'uid' & 'token' are undefined
            if(!uid && !token){
                actions.changePassword(oldpassword, newpassword)
                .then(
                    function(response) {
                        setPassword.setState({
                            oldpassword: '',
                            newpassword: '',
                            password: '',
                            error: false,
                            message: 'Đổi mật khẩu thành công! Yêu cầu đăng nhập lại!'
                        })

                        //Logout & ask for log in with new password
                        setTimeout(
                            function(){
                                actions.logout().then( () => {
                                        browserHistory.push('/');
                                    }
                                );
                            },
                        200);
                    }
                ).catch(
                    function(error) {
                        setPassword.setState({
                            error: true,
                            message: error.response.data.message
                        })
                    }
                )
            }

            //SET PASSWORD case, 'uid' & 'token' are defined
            else {
                const user = {
                    uid: uid,
                    securityToken: token,
                    password: newpassword
                }

                actions.setPassword(user)
                .then(
                    function(response) {
                        setPassword.setState({
                            oldpassword: '',
                            newpassword: '',
                            password: '',
                            error: false,
                            message: 'Thiết lập thành công! Yêu đăng nhập lại!'
                        })

                        //Logout & ask for log in with new password
                        setTimeout(
                            function(){
                                browserHistory.push('/');
                            },
                        500);
                    }
                ).catch(
                    function(error) {
                        setPassword.setState({
                            error: true,
                            message: error.response.data.message
                        })
                    }
                )
            }
        }
        else {
            setPassword.setState({
                error: true,
                message: 'Mật khẩu mới không trùng khớp!'
            })
        }
    }

    render() {
        const { uid, token } = this.props.params;
        const { actions } = this.props;
        const { oldpassword, newpassword, password, error, message } = this.state;

        return (

            <div>
                <Navbar />

                <form class="form-horizontal set-password-form" onSubmit={this.handleSubmit}>
                    <h3>{ !uid && !token ? 'Sửa đổi mật khẩu' : 'Thiết lập mật khẩu' }</h3>
                    <div class="send-mail-warning">
                        * Độ dài mật khẩu phải trên 6 kí tự.
                    </div>

                    <div class="set-password-content">

                        { !uid && !token ?
                            <div class="form-group">
                                <label class="col-sm-3 control-label old-password">Mật khẩu cũ: </label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" placeholder="Mật khẩu" value={oldpassword}
                                        onChange={(e) => this.setState({
                                            oldpassword: e.target.value,
                                            error: false,
                                            message: ''
                                        })}
                                        required
                                    />
                                </div>
                            </div>  : ''
                        }

                        <div class="form-group">
                            <label class="col-sm-3 control-label">* Mật khẩu {!uid && !token ? 'mới' : '' } : </label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" placeholder="Mật khẩu" value={newpassword}
                                    onChange={(e) => this.setState({
                                        newpassword: e.target.value,
                                        error: false,
                                        message: ''
                                    })}
                                    required
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nhập lại mật khẩu {!uid && !token ? 'mới' : '' }: </label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" placeholder="Nhập lại mật khẩu" value={password}
                                    onChange={(e) => this.setState({
                                        password: e.target.value,
                                        error: false,
                                        message: ''
                                    })}
                                    required
                                />
                            </div>
                        </div>

                        { !error &&
                            <div class="send-mail-success">
                                {message}
                            </div>
                        }

                        { error &&
                            <div class="send-mail-error">
                                Có lỗi xảy ra: {message}
                            </div>
                        }
                    </div>

                    <Link class="set-password-back-home pull-left" to="/">
                        Quay trở lại trang chủ
                    </Link>

                    <div class="set-password-footer pull-right">
                        <button type="submit" class="btn btn-primary">{!uid && !token ? 'Cập nhật' : 'Thiết lập' }</button>
                        <button type="button" class="btn btn-default" onClick={() => this.refreshForm()}>Làm mới</button>
                    </div>
                </form>
            </div>
        )
    }
}

export default SetPassword
