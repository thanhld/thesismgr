import React, { Component } from 'react'

class ForgotPassword extends Component {
    constructor() {
        super();
        this.state = {
            mail: '',
            error: false,
            message: '',
            sending: false
        }

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleSubmit(e) {
        e.preventDefault();

        this.setState({
            error: false,
            message: ''
        })

        const { actions } = this.props;
        const { mail } = this.state;
        const forgotPassword = this;

        const emailRegex = /^$|^\S+@\S+$/
        if (!emailRegex.test(mail)) {
            this.setState({
                error: true,
                message: 'Địa chỉ VNU email không hợp lệ!'
            })
            return false
        }

        this.setState({
            sending: true
        })

        actions.forgotPassword(mail)
        .then(
            function(response){
                console.log(response);

                forgotPassword.setState({
                    mail: '',
                    error: false,
                    message: 'Gửi thành công! Vui lòng kiểm tra mail và xác nhận.',
                    sending: false
                })
            }
        )
        .catch(
            function(error){
                console.log(error);

                forgotPassword.setState({
                    error: true,
                    message: 'Địa chỉ VNU email không có trong hệ thống!',
                    sending: false
                })
            }
        );
    }

    render() {
        const { mail, error, message, sending } = this.state;

        return (
            <div id="forgotPassword" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="forgotPasswordModal">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="forgotPasswordModal">Quên mật khẩu?</h4>
                        </div>

                        <form class="form-horizontal forgot-password-form" onSubmit={this.handleSubmit}>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Nhập VNU email:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Email" value={mail}
                                            onChange={(e) => this.setState({
                                                mail: e.target.value,
                                                error: false,
                                                message: ''
                                            })} />
                                    </div>
                                </div>

                                { !error &&
                                <div class="send-mail-success">
                                    {message}
                                </div>
                                }

                                { error &&
                                <div class="send-mail-error">
                                    {message}
                                </div>
                                }
                                { sending && <div class="text-center">
                                    <i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
                                    <span class="sr-only">Loading...</span>
                                </div> }
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Gửi</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Quay lại</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default ForgotPassword
