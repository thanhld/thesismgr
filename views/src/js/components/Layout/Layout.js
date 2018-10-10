import React, { Component } from 'react'
import { browserHistory } from 'react-router'
import LoadingBar from 'react-redux-loading-bar'
import {
    Navbar,
    Notification,
    Sidebar,
} from 'Components'
import { ADMIN_LOAD_OUT_TOPICS } from 'Constants'
import { isAdmin, isLecturer } from 'Helper'

class Layout extends Component {
    constructor(props) {
        super(props)
        this.state = {
            sidebar: localStorage.getItem('sidebar_type') || 'info'
        }
    }
    componentWillMount() {
        const { auth: {user}, actions, topics, adminOutTopics } = this.props
        const filter = `outOfficerIds<>null`
        if (isAdmin(user) && !adminOutTopics.isLoaded) actions.adminLoadTopics(ADMIN_LOAD_OUT_TOPICS, filter)
        if (isLecturer(user) && !topics.isLoaded) actions.loadTopics(user.uid)
    }
    changeInputMessage = () => {
        $(document).ready(() => {
            let inpElements = document.getElementsByTagName('INPUT')
            for (let i = 0; i < inpElements.length; i++) {
                inpElements[i].oninvalid = e => {
                    e.target.setCustomValidity('')
                    if (!e.target.validity.valid) {
                        e.target.setCustomValidity('Vui lòng điền thông tin.')
                    } else if (!e.target.validity.typeMismatch) {
                        e.target.setCustomValidity('Vui lòng nhập kiểu thông tin chính xác.')
                    } else if (!e.target.validity.rangeUnderflow) {
                        e.target.setCustomValidity('Giá trị nhập quá lớn.')
                    } else if (!e.target.validity.rangeOverflow) {
                        e.target.setCustomValidity('Giá trị nhập quá nhỏ.')
                    }
                }
                inpElements[i].oninput = e => {
                    e.target.setCustomValidity('')
                }
            }
            let selectElements = document.getElementsByTagName('SELECT')
            for (let i = 0; i < selectElements.length; i++) {
                selectElements[i].oninvalid = e => {
                    e.target.setCustomValidity('')
                    if (!e.target.validity.valid) {
                        e.target.setCustomValidity('Vui lòng chọn một mục trong danh sách.')
                    }
                }
                selectElements[i].oninput = e => {
                    e.target.setCustomValidity('')
                }
            }
            let textElements = document.getElementsByTagName('TEXTAREA')
            for (let i = 0; i < textElements.length; i++) {
                textElements[i].oninvalid = e => {
                    e.target.setCustomValidity('')
                    if (!e.target.validity.valid) {
                        e.target.setCustomValidity('Vui lòng điền thông tin.')
                    }
                }
                textElements[i].oninput = e => {
                    e.target.setCustomValidity('')
                }
            }
        })
    }
    componentDidMount() {
        this.changeInputMessage()
    }
    componentDidUpdate() {
        this.changeInputMessage()
    }
    render() {
        const { sidebar } = this.state
        const { auth, actions, topics, adminOutTopics } = this.props
        return (
            <div>
                <LoadingBar class="loading-bar" />
                <Navbar
                    user={auth.user}
                    actions={actions}
                     />
                <div class="container-fluid">
                    <Sidebar user={auth.user} topics={topics} adminOutTopics={adminOutTopics} sidebar={sidebar} />
                    <div class="container-fluid">
                        <div class="content">
                            {this.props.children}
                        </div>
                    </div>
                </div>
                <Notification />
            </div>
        )
    }
}

export default Layout
