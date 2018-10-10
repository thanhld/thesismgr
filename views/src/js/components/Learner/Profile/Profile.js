import React, { Component } from 'react'
import { notify, Loading } from 'Components'

class LearnerProfile extends Component {
    constructor() {
        super()
        this.state = {
            otherEmail: "",
            phone: "",
            gpa: "",
            editOtherEmail: false,
            editPhone: false,
            editGPA: false
        }
    }
    componentWillMount() {
        if (!this.props.learner.isLoaded) this.reloadProfile()
    }
    componentWillReceiveProps(nextProps) {
        const { learner: { data } } = nextProps
        this.setState({
            phone: data.phone,
            otherEmail: data.otherEmail,
            gpa: data.gpa
        })
    }
    reloadProfile = callback => {
        const { user, actions } = this.props
        actions.loadProfile(user.uid).then(callback)
    }
    updateProfile = (data, callback) => {
        const { user, actions } = this.props
        actions.updateProfile(user.uid, data).then(() => {
            this.reloadProfile(() => {
                callback()
                notify.show(`Thông tin cá nhân của bạn đã được cập nhật`, 'primary')
            })
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    submitOtherEmail = () => {
        const { learner } = this.props
        const { otherEmail } = this.state
        // Check email
        const emailRegex = /^$|^\S+@\S+$/
        if (!emailRegex.test(otherEmail)) {
            notify.show('Địa chỉ email không đúng. Vui lòng nhập lại.', 'danger')
            return false
        }
        const data = {...learner.data, otherEmail: otherEmail && otherEmail.trim()}
        this.updateProfile(data, () => {
            this.setState({
                editOtherEmail: false
            })
        })
    }
    submitPhoneNumber = () => {
        const { learner } = this.props
        const { phone } = this.state
        // Check phone number
        const phoneNumberRegex = /^[0-9 ]*$/
        if (!phoneNumberRegex.test(phone)) {
            notify.show('Số điện thoại không đúng. Vui lòng nhập lại.', 'danger')
            return false
        }
        const data = {...learner.data, phone: phone && phone.trim()}
        this.updateProfile(data, () => {
            this.setState({
                editPhone: false
            })
        })
    }
    submitGPA = () => {
        const { learner } = this.props
        const { gpa } = this.state
        // Check GPA
        if (gpa > 4.0 || gpa < 0.0) {
            notify.show('Điểm trung bình không hợp lệ. Vui lòng nhập lại.', 'danger')
            return false
        }
        const data = {...learner.data, gpa: gpa || '0'}
        this.updateProfile(data, () => {
            this.setState({
                editGPA: false
            })
        })
    }
    render() {
        const { otherEmail, phone, gpa, editOtherEmail, editPhone, editGPA } = this.state
        const tmp = this.props.learner
        if (!tmp.isLoaded) return <Loading />
        const learner = tmp.data
        return (
            <div class="profile-box">
                <br />
                <div class="row">
                    <div class="hidden-xs col-sm-offset-1 col-sm-3">
                        <img class="img-rounded img-responsive" src={ learner.avatarUrl || "/images/brand-logo.jpg" } />
                    </div>

                    <div class="col-xs-12 col-sm-offset-1 col-sm-6">
                        <div class="profile-info-1">
                            {/*<i class="fa fa-user"></i>*/}
                            {learner.fullname}
                        </div>
                        <div class="profile-info-2">
                            <i class="fa fa-address-card"></i>
                            Mã sinh viên: <span>{learner.learnerCode}</span>
                        </div>
                        <div class="profile-info-2">
                            <i class="fa fa-envelope"></i>
                            VNU email: <span>{learner.vnuMail}</span>
                        </div>
                        <div class="profile-info-2">
                            <i class="fa fa-envelope-o"></i>
                            Email khác: {!editOtherEmail && <span><i>{learner.otherEmail || <span>
                                Chưa có
                            </span>}</i></span>}
                            {!editOtherEmail &&
                                <i class="fa fa-edit" title="Nhấn để sửa email khác" onClick={e => this.setState({editOtherEmail: true})}></i>}
                            { editOtherEmail &&
                                <span>
                                    <input type="text" class="input-lecture-profile" size="50" value={otherEmail || ''} onChange={e => this.setState({otherEmail: e.target.value})} />
                                    {" "}
                                    <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitOtherEmail}></i>
                                    <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editOtherEmail: false, otherEmail: learner.otherEmail})}></i>
                                </span> }
                        </div>
                        <div class="profile-info-2">
                            <i class="fa fa-phone"></i>
                            Số điện thoại: {!editPhone && <span><i>{learner.phone || <span>
                                Chưa có
                            </span>}</i></span>}
                            {!editPhone &&
                                <i class="fa fa-edit" title="Nhấn để sửa Số điện thoại" onClick={e => this.setState({editPhone: true})}></i>}
                            { editPhone &&
                                <span>
                                    <input type="text" class="input-lecture-profile" size="50" value={phone || ''} onChange={e => this.setState({phone: e.target.value})} />
                                    {" "}
                                    <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitPhoneNumber}></i>
                                    <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editPhone: false, phone: learner.phone})}></i>
                                </span> }
                        </div>
                        <div class="profile-info-2">
                            <i class="fa fa-phone"></i>
                            Điểm tích lũy: {!editGPA && <span><i>{learner.gpa || <span>
                                Chưa có
                            </span>}</i></span>}
                            {!editGPA &&
                                <i class="fa fa-edit" title="Nhấn để sửa Điểm tích lũy" onClick={e => this.setState({editGPA: true})}></i>}
                            { editGPA &&
                                <span>
                                    <input type="number" step="0.01" class="input-lecture-profile" size="50" value={gpa || ''} onChange={e => this.setState({gpa: e.target.value})} />
                                    {" "}
                                    <i class="fa fa-check text-success" title="Nhấn để lưu" onClick={this.submitGPA}></i>
                                    <i class="fa fa-times text-danger" title="Nhấn để bỏ qua" onClick={e => this.setState({editGPA: false, gpa: learner.gpa})}></i>
                                </span> }
                        </div>
                    </div>
                </div>
                <br />
            </div>
        )
    }
}

export default LearnerProfile
