import React, { Component } from 'react'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import classnames from 'classnames'
import { ADMIN_LOAD_TOPICS } from 'Constants'
import TopicsList from './TopicsList'
import TopicsRegister from './Register/Register'
import TopicsWorking from './Working/Working'
import TopicsEditing from './Editing/Editing'
import TopicsExtending from './Extending/Extending'
import TopicsPause from './Pause/Pause'
import TopicsCancel from './Cancel/Cancel'
import TopicsRegisterDefense from './RegisterDefense/RegisterDefense'
import TopicsDefense from './Defense/Defense'
import TopicsSeminar from './Seminar/Seminar'
import { routeName } from 'Config'

const TOPIC_NAME = {
    [routeName['ADMIN_TOPIC_STUDENT']]: 'khóa luận tốt nghiệp',
    [routeName['ADMIN_TOPIC_GRADUATED']]: 'luận văn cao học',
    [routeName['ADMIN_TOPIC_RESEARCHER']]: 'luận án tiến sĩ'
}

const TOPIC_TYPE = {
    [routeName['ADMIN_TOPIC_STUDENT']]: '1',
    [routeName['ADMIN_TOPIC_GRADUATED']]: '2',
    [routeName['ADMIN_TOPIC_RESEARCHER']]: '3'
}
const TOPIC_STATUS_RANGE = {
    0: { // Đăng ký
        lowerStatus: '100',
        higherStatus: '104'
    },
    1: { // Đang thực hiện
        lowerStatus: '887',
        higherStatus: '895'
    },
    2: { // Điều chỉnh
        lowerStatus: '890',
        higherStatus: '892'
    },
    3: { // Gia hạn
        lowerStatus: '893',
        higherStatus: '895'
    },
    4: { // Tạm hoãn
        lowerStatus: '300',
        higherStatus: '303'
    },
    5: { // Xin thôi
        lowerStatus: '200',
        higherStatus: '202'
    },
    6: { // Kiểm tra tiến độ bộ môn
        lowerStatus: '897',
        higherStatus: '900'
    },
    7: { // Đăng ký bảo vệ
        lowerStatus: '666',
        higherStatus: '670'
    },
    8: { // Bảo vệ
        lowerStatus: '700',
        higherStatus: '700'
    },
    9: { // Kết thúc
        lowerStatus: '0',
        higherStatus: '3'
    }
}

class AdminTopics extends Component {
    constructor(props) {
        super(props)
        this.state = {
            activePage: sessionStorage.getItem('page') || 1,
            lowerStatus: TOPIC_STATUS_RANGE[sessionStorage.getItem('selected-tab') || 0].lowerStatus,
            higherStatus: TOPIC_STATUS_RANGE[sessionStorage.getItem('selected-tab') || 0].higherStatus,
            type: TOPIC_TYPE[props.params.type],
            needPrint: false
        }
    }
    componentWillReceiveProps(nextProps) {
        const { params: { type } } = this.props
        const nextType = nextProps.params.type
        if (type != nextType) this.setState({
            type: TOPIC_TYPE[nextType]
        }, this.reloadData)
    }
    handleSelect = index => {
        sessionStorage.setItem('selected-tab', index)
        const { actions } = this.props
        actions.flushTopics()
        const nextRange = TOPIC_STATUS_RANGE[index]
        const { lowerStatus, higherStatus } = nextRange
        this.setState({lowerStatus, higherStatus, activePage: 1}, this.reloadData)
        sessionStorage.setItem('page', 1)
    }
    reloadData = callback => {
        const { actions } = this.props
        const { lowerStatus, higherStatus, type } = this.state
        const filter = `topicStatus>=${lowerStatus},topicStatus<=${higherStatus},topicType=${type}`
        actions.loadTopics(ADMIN_LOAD_TOPICS, filter).then(() => {
            if (callback) callback()
        })
    }
    componentWillMount() {
        const { facultyId, actions, departments, degrees, lecturers } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!departments.isLoaded) actions.loadDepartmentOfFaculty(facultyId)
        this.reloadData()
    }
    getCurrentIndex = () => {
        let index = sessionStorage.getItem('selected-tab') || 0
        const notStudentTab = [3, 4]
        const { params: { type } } = this.props
        if (type == routeName['ADMIN_TOPIC_STUDENT'] && notStudentTab.find(n => n == index) >= 0) return parseInt(0)
        return parseInt(index)
    }
    handlePageChange = pageNum => {
        sessionStorage.setItem('page', pageNum)
        this.setState({
            activePage: pageNum
        })
    }
    updateNeedPrint = bool => {
        this.setState({
            needPrint: bool
        })
    }
    componentWillUnmount() {
        const { actions } = this.props
        actions.flushTopics()
    }
    render() {
        const { params: { type }, topics: { list }, actions, departments, degrees, lecturers } = this.props
        const { activePage, lowerStatus, higherStatus, needPrint } = this.state
        const tabClass = classnames({
            'hidden': type == routeName['ADMIN_TOPIC_STUDENT']
        })

        return <div>
            <div class="row">
                <div class="col-xs-9 page-title">Quản lý {TOPIC_NAME[type]}</div>
            </div>
            <Tabs
                selectedIndex={this.getCurrentIndex()}
                onSelect={this.handleSelect}>
                <TabList>
                    <Tab>Đăng ký đề tài</Tab>
                    <Tab>Đang thực hiện</Tab>
                    <Tab>Điều chỉnh</Tab>
                    <Tab class={tabClass}>Gia hạn</Tab>
                    <Tab class={tabClass}>Tạm hoãn</Tab>
                    <Tab>Xin thôi</Tab>
                    <Tab class={tabClass}>Kiểm tra tiến độ</Tab>
                    <Tab>Đăng ký bảo vệ</Tab>
                    <Tab>Bảo vệ</Tab>
                    <Tab>Kết thúc</Tab>
                </TabList>
                <TabPanel> {/* Đăng ký */}
                    <TopicsRegister
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Đang thực hiện */}
                    <TopicsWorking
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Điều chỉnh */}
                    <TopicsEditing
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel class={tabClass}> {/* Gia hạn */}
                    <TopicsExtending
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel class={tabClass}> {/* Tạm hoãn */}
                    <TopicsPause
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Xin thôi */}
                    <TopicsCancel
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Kiểm tra tiến độ bộ môn */}
                    <TopicsSeminar
                        type={type}
                        topics={list}
                        actions={actions}
                        needPrint={needPrint}
                        updateNeedPrint={this.updateNeedPrint}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Đăng ký bảo vệ */}
                    <TopicsRegisterDefense
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Bảo vệ */}
                    <TopicsDefense
                        type={type}
                        topics={list}
                        actions={actions}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        reloadData={this.reloadData}
                    />
                </TabPanel>
                <TabPanel> {/* Kết thúc */}
                </TabPanel>
            </Tabs>
            <TopicsList
                topics={list}
                actions={actions}
                activePage={activePage}
                tabpanel={sessionStorage.getItem('selected-tab') || 0}
                degrees={degrees.list}
                lecturers={lecturers.list}
                departments={departments.list}
                lowerStatus={lowerStatus}
                higherStatus={higherStatus}
                reloadData={this.reloadData}
                handlePageChange={this.handlePageChange}
                needPrint={needPrint}
                updateNeedPrint={this.updateNeedPrint}
            />
        </div>
    }
}

export default AdminTopics
