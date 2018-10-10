import React, { Component } from 'react'

class AdminManageTopicChange extends Component {
    render() {
        const { type, checked, topics, degrees, lecturers, error, message, handleCheck, handleToggleCheckAll } = this.props
        return <div>
            { error && <div>
                <div class="text-message-error">
                    Đề tài chưa được lưu thành công: { message }
                </div>
                <br />
            </div> }
            <div class="text-center">
                Danh sách đề tài được đề nghị
            </div>
            <div class="table-responsive review-table-container">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th class="col-xs-1">
                                <input
                                    type="checkbox"
                                    checked={!checked.includes(false)}
                                    onChange={handleToggleCheckAll} />
                            </th>
                            <th class="col-xs-2">Mã sinh viên</th>
                            <th class="col-xs-6">Tên đề tài</th>
                            <th class="col-xs-3">Giảng viên chính</th>
                        </tr>
                    </thead>
                    <tbody>
                        { topics.length > 0 && topics.map((topic, index) => {
                            const lecturer = lecturers.find(l => l.id == topic.mainSupervisorId)
                            const degree = lecturer && degrees.find(d => d.id == lecturer.degreeId)
                            return (
                                <tr key={topic.id}>
                                    <td>
                                        <input type="checkbox" checked={!!checked[index]} onChange={handleCheck(index)} />
                                    </td>
                                    <td>{topic.learner && topic.learner.learnerCode}</td>
                                    <td>{topic.isEnglish ? topic.englishTopicTitle : topic.vietnameseTopicTitle}</td>
                                    <td>{degree && `${degree.name}.`} {lecturer && lecturer.fullname}</td>
                                </tr>
                            )})}
                    </tbody>
                </table>
            </div>
        </div>
    }
}

export default AdminManageTopicChange
