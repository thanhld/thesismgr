import React, { Component } from 'react'
import { isOfficerAdmin } from 'Helper'
import { Loading, notify } from 'Components'
import Announcement from './Announcement/Announcement'
import UpdateAnnouncement from './Announcement/UpdateAnnouncement'

class Home extends Component {
    constructor(props) {
        super(props)
        this.state = {
            current_action: '',
            current_announcement: {}
        }
    }
    reloadData = callback => {
        const { actions, user } = this.props
        if (isOfficerAdmin(user)) {
            actions.adminLoadAnnouncements().then(() => {
                callback()
            })
        } else {
            actions.loadAnnouncements().then(() => {
                callback()
            })
        }
    }
    componentWillMount() {
        const { actions, user, announcements } = this.props
        if (!announcements.isLoaded) {
            if (isOfficerAdmin(user)) {
                actions.adminLoadAnnouncements()
            } else actions.loadAnnouncements()
        }
    }
    setCurrentAnnounce = current_announcement => e => {
        this.setState({
            current_action: 'update',
            current_announcement: current_announcement
        })
    }
    deleteAnnounce = current_announcement => e => {
        const val = confirm(`Thầy/cô có muốn xóa thông báo này?`)
        if (val) {
            const { actions } = this.props
            actions.deleteAnnouncement(current_announcement.id).then(() => {
                this.reloadData(() => {
                    notify.show(`Thông báo được xóa thành công`, 'primary')
                })
            }).catch(err => {
                console.log(err);
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    render() {
        const { actions, user, announcements } = this.props
        const { current_action, current_announcement } = this.state
        if (!announcements.isLoaded) return <Loading />
        return (
            <div>
                <div class="page-title text-center">
                    Thông báo chung
                    { isOfficerAdmin(user) &&
                        <button class="pull-right btn btn-sm btn-success" data-toggle="modal" data-target="#updateAnnounce" onClick={e => this.setState({
                            current_action: 'create',
                            current_announcement: {}
                        })}>
                            Thêm mới
                        </button>
                    }
                </div>
                { announcements.isLoaded && announcements.list.map(a => { return (
                    <Announcement
                        key={a.id}
                        user={user}
                        announcement={a}
                        setCurrentAnnounce={this.setCurrentAnnounce}
                        deleteAnnounce={this.deleteAnnounce}
                    />
                )})}
                { isOfficerAdmin(user) &&
                    <UpdateAnnouncement
                        modalId="updateAnnounce"
                        action={current_action}
                        actions={actions}
                        reloadData={this.reloadData}
                        announcement={current_announcement}
                    />
                }
            </div>
        )
    }
}

export default Home
