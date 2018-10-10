import React, { Component } from 'react'
import { isNeedToConfirmTopic, isTopicEdit } from 'Helper'
import SidebarNavLink from './SidebarNavLink'
import { routeName } from 'Config'

class SidebarLecturer extends Component {
    isConfirmLecturer = topic => {
        const { user: {uid} } = this.props
        const { requestedSupervisorId, topicChange, topicStatus } = topic
        if (!isNeedToConfirmTopic(topicStatus)) return false
        if (isTopicEdit(topicStatus)) return topicChange && topicChange.requestedSupervisorId == uid
        return requestedSupervisorId == uid
    }
    render() {
        const { user, topics } = this.props
        const listRequest = topics.list.filter(t => this.isConfirmLecturer(t))
        const numRequest = listRequest.length
        const numStudentRequest = listRequest.filter(t => t.topicType == 1).length
        const numGraduatedRequest = listRequest.filter(t => t.topicType == 2).length
        const numResearcherRequest = listRequest.filter(t => t.topicType == 3).length
        return (
            <ul class="nav nav-pills nav-stacked left-slide-bar">
                <li class="li-left-slidebar" onClick={this.props.closebar}>
                    <SidebarNavLink to={`/${routeName['LECTURER']}/${routeName['LECTURER_PROFILE']}`}>
                        <i class="fa fa-user-o fa-lg fa-fw" aria-hidden="true"></i>
                        <span> Thông tin cá nhân </span>
                    </SidebarNavLink>
                </li>
                    <li class="li-left-slidebar" onClick={this.props.closebar}>
                        <SidebarNavLink to={`/${routeName['LECTURER']}/${routeName['LECTURER_TOPIC']}/${routeName['LECTURER_STUDENT']}`}>
                            <i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>
                            <span> Hướng dẫn KLTN/ĐATN
                                {numStudentRequest > 0 && <div class="badge badge-danger pull-right">{numStudentRequest}</div>}
                            </span>
                        </SidebarNavLink>
                    </li>
                  {/*  <li class="li-left-slidebar" onClick={this.props.closebar}>
                        <SidebarNavLink to={`/${routeName['LECTURER']}/${routeName['LECTURER_TOPIC']}/${routeName['LECTURER_GRADUATED']}`}>
                            <i class="fa fa-genderless fa-lg fa-fw" aria-hidden="true"></i>
                            <span> Hướng dẫn LVCH
                                {numGraduatedRequest > 0 && <div class="badge badge-danger pull-right">{numGraduatedRequest}</div>}
                            </span>
                        </SidebarNavLink>
                    </li>
                    <li class="li-left-slidebar" onClick={this.props.closebar}>
                        <SidebarNavLink to={`/${routeName['LECTURER']}/${routeName['LECTURER_EXPERTISE']}`}>
                            <i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>
                            <span> Phản biện đề cương </span>
                        </SidebarNavLink>
                    </li> */}
            </ul>
        )
    }
}

export default SidebarLecturer
