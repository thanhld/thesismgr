import React, { Component } from 'react'
import Pagination from 'react-js-pagination'
import { reviewStatusToText } from 'Helper'
import { Link } from 'react-router'
import { ITEM_PER_PAGE, PAGE_RANGE } from 'Config'
import AssignReviewer from './AssignReviewer'

class TopicsList extends Component {
    render() {
        const { actions, topics, lecturers, degrees } = this.props
        const { activePage, current_topic } = this.props
        const topicLength = topics.list.length
        return <div>
            { topics.list.length == 0 && <div>Hiện không có đề tài thẩm định.</div> }
            { topics.list.length > 0 && <div><br /><div>
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th class="col-xs-1">Mã học viên</th>
                            <th class="col-xs-1">Tên học viên</th>
                            <th class="col-xs-2">Tên đề tài</th>
                            <th class="hidden-xs col-sm-2">GVHD chính</th>
                            <th class="col-xs-3">Phản biện</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        { topics.list && topics.list.slice(ITEM_PER_PAGE * (activePage - 1), ITEM_PER_PAGE * activePage).map(topic => {
                            const lecturer = lecturers.list.find(l => l.id == topic.mainSupervisorId)
                            const degree = lecturer && degrees.list.find(d => d.id == lecturer.degreeId)
                            return (
                                <tr key={topic.id}>
                                    <td>{topic.learner.learnerCode}</td>
                                    <td>{topic.learner.fullname}</td>
                                    <td>{topic.vietnameseTopicTitle}</td>
                                    <td>{degree && `${degree.name}.`} {lecturer && lecturer.fullname}</td>
                                    <td>
                                        {topic.reviews && topic.reviews.length && topic.reviews.map(review => {
                                            const reviewer = lecturers.list.find(l => l.id == review.officerId)
                                            const d_review = reviewer && degrees.list.find(d => d.id == reviewer.degreeId)
                                            return (
                                                <div key={review.id}>PB lần {review.iteration}: {d_review && `${d_review.name}.`} {reviewer && reviewer.fullname} { reviewStatusToText(review.reviewStatus) } </div>
                                            )
                                        })}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-margin" data-toggle="modal" data-target="#assignReviewer" onClick={e => this.props.changeCurrentTopic(topic)}>Phân công PB</button>
                                        <span class="dropdown">
                                            <button type="button" class="btn btn-primary btn-sm btn-margin dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Quyết định <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="clickable" onClick={e => this.props.departmentApprove(topic, 5001, e)}>Đạt</a></li>
                                                <li><a class="clickable" onClick={e => this.props.departmentApprove(topic, 5000, e)}>Không đạt</a></li>
                                                <li><a class="clickable" onClick={e => this.props.departmentApprove(topic, 5002, e)}>Cần chỉnh sửa</a></li>
                                            </ul>
                                        </span>
                                        <Link to={`/topic/${topic.id}`} class="btn btn-sm btn-primary btn-margin">Chi tiết</Link>
                                    </td>
                                </tr>
                            )})}
                    </tbody>
                </table>
            </div>
                <div class="text-center">
                    <Pagination
                        activePage={activePage}
                        itemsCountPerPage={ITEM_PER_PAGE}
                        totalItemsCount={topicLength}
                        pageRangeDisplayed={PAGE_RANGE}
                        onChange={this.props.handlePageChange}
                    />
                </div></div> }
            <AssignReviewer
                modalId={'assignReviewer'}
                title={'Phân công phản biện đề cương luận văn cao học'}
                actions={actions}
                degrees={degrees.list}
                lecturers={lecturers.list}
                topic={current_topic}
                reloadData={this.props.reloadData}
            />
        </div>
    }
}

export default TopicsList
