import React, { Component } from 'react'
import moment from 'moment'
import { isOfficerAdmin } from 'Helper'

class Announcement extends Component {
    render() {
        const { user, announcement, setCurrentAnnounce, deleteAnnounce } = this.props
        const { title, content, tags, showDate, hideDate } = announcement
        return <div class="announcement">
            <div class="row">
                <div class="col-xs-9 ann-title">{title}</div>
                <div class="col-xs-3">
                    <div class="pull-right">
                        { isOfficerAdmin(user) && <div>
                            <button class="btn btn-sm btn-margin btn-primary" data-toggle="modal" data-target="#updateAnnounce" onClick={setCurrentAnnounce(announcement)}>Chỉnh sửa</button>
                            <button class="btn btn-sm btn-margin btn-primary" onClick={deleteAnnounce(announcement)}>Xóa</button>
                        </div>
                        }
                    </div>
                </div>
            </div>
            <div class="ann-user">
                Vào lúc: {moment(showDate).format('HH:mm DD-MM-YYYY')}
                { isOfficerAdmin(user) && <span>; Ẩn vào: {moment(hideDate).format('HH:mm DD-MM-YYYY')}</span> }
            </div>
            <div class="ann-content wrap-text">{content}</div>
            { tags && <div class="ann-tags">Tags: {tags}</div> }

            <hr />
        </div>
    }
}

export default Announcement
