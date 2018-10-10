import React, { Component } from 'react'
import { notify } from 'Components'
import QuotasCreate from './QuotasCreate'
import QuotasUpdate from './QuotasUpdate'

class SuperuserQuotas extends Component {
    constructor(props) {
        super(props)
        this.state = {
            changeQuota: {}
        }
    }
    componentWillMount() {
        const { degrees, quotas, actions } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!quotas.isLoaded) actions.loadQuotas()
    }
    reloadData = callback => {
        const { actions } = this.props
        actions.loadQuotas().then(() => {
            if (callback) callback()
        })
    }
    transformQuotas = () => {
        const { quotas } = this.props
        if (!quotas.isLoaded) return []
        if (quotas.list.length == 0) return []
        let newQuotas = [...quotas.list]
        const sortQuotas = (a, b) => {
            if (a.version < b.version) return -1
            if (a.version > b.version) return 1
            return 0
        }
        newQuotas = newQuotas.sort(sortQuotas)
        let resultQuotas = []
        let cur_ver = newQuotas[0].version
        let cur_data = []
        newQuotas.forEach((q, idx) => {
            if (q.version != cur_ver) {
                resultQuotas.push({
                    version: newQuotas[idx - 1].version,
                    isActive: newQuotas[idx - 1].isActive,
                    data: cur_data,
                    mainFactor: [newQuotas[idx - 1].mainFactorStudent, newQuotas[idx - 1].mainFactorGraduated, newQuotas[idx - 1].mainFactorResearcher],
                    coFactor: [newQuotas[idx - 1].coFactorStudent, newQuotas[idx - 1].coFactorGraduated, newQuotas[idx - 1].coFactorResearcher]
                })
                cur_data = []
                cur_ver = q.version
            }
            const { id, degreeId, maxStudent, maxGraduated, maxResearcher } = q
            cur_data.push({
                id, degreeId, maxStudent, maxGraduated, maxResearcher
            })
        })
        resultQuotas.push({
            version: newQuotas[newQuotas.length - 1].version,
            isActive: newQuotas[newQuotas.length - 1].isActive,
            data: cur_data,
            mainFactor: [newQuotas[newQuotas.length - 1].mainFactorStudent, newQuotas[newQuotas.length - 1].mainFactorGraduated, newQuotas[newQuotas.length - 1].mainFactorResearcher],
            coFactor: [newQuotas[newQuotas.length - 1].coFactorStudent, newQuotas[newQuotas.length - 1].coFactorGraduated, newQuotas[newQuotas.length - 1].coFactorResearcher]
        })
        return resultQuotas
    }
    changeActiveQuota = (version, isActive) => () => {
        const { actions } = this.props
        actions.changeActiveQuota(version, 1 - isActive).then(res => {
            this.reloadData(() => {
                notify.show(`Thay đổi định mức thành công`, 'primary')
            })
        }).catch(err => {
            notify.show(err.response.data.message, 'danger')
        })
    }
    deleteQuota = version => () => {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa định mức này?`)
        if (!val) return
        const { actions } = this.props
        actions.deleteQuota(version).then(res => {
            this.reloadData(() => {
                notify.show(`Xóa định mức thành công`, 'primary')
            })
        }).catch(err => {
            notify.show(err.response.data.message, 'danger')
        })
    }
    changeNextQuota = nextQuota => () => {
        this.setState({
            changeQuota: {
                version: nextQuota.version,
                data: nextQuota.data,
                mainFactor: nextQuota.mainFactor,
                coFactor: nextQuota.coFactor
            }
        })
    }
    render() {
        const { changeQuota } = this.state
        const { degrees, quotas, actions } = this.props
        const transformedQuotas = this.transformQuotas()
        return <div>
            <div class="row">
                <div class="col-xs-9">
                    <div class="page-title">Định mức, hệ số hướng dẫn</div>
                </div>
                <div class="col-xs-3">
                    <div class="pull-right">
                        <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#createQuotas">
                            Thêm mới
                        </button>
                    </div>
                </div>
            </div>
            <br />
            <div class="table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Phiên bản</th>
                            <th>Học hàm, học vị</th>
                            <th class="col-xs-2">Khóa luận</th>
                            <th class="col-xs-2">Luận văn</th>
                            <th class="col-xs-2">Luận án</th>
                            <th class="col-xs-1">Đang sử dụng</th>
                            <th class="col-xs-1">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        { transformedQuotas.map(q => {
                            return [q.data.map((quota, idx) => {
                                let degreeName = degrees.list.find(d => d.id == quota.degreeId)
                                if (idx < q.data.length) return <tr key={quota.id}>
                                    { idx == 0 && <td rowSpan={q.data.length + 2}>{q.version}</td> }
                                    <td>{degreeName.name}</td>
                                    <td>{quota.maxStudent}</td>
                                    <td>{quota.maxGraduated}</td>
                                    <td>{quota.maxResearcher}</td>
                                    { idx == 0 && <td rowSpan={q.data.length} class="text-center">{q.isActive == 0 ? <i class="fa fa-close text-danger" aria-hidden="true"></i> : <i class="fa fa-check text-success" aria-hidden="true"></i>}</td> }
                                    { idx == 0 && <td rowSpan={q.data.length}>
                                        <div class="margin-bottom-sm">
                                            <button class="btn btn-sm btn-primary btn-margin" onClick={this.changeActiveQuota(q.version, q.isActive)}>{q.isActive == 0 ? "Sử dụng" : "Tắt sử dụng"}</button>
                                        </div>
                                        { q.isActive == 0 && <button class="btn btn-sm btn-primary btn-margin" data-toggle="modal" data-target="#updateQuotas" onClick={this.changeNextQuota(q)}>Sửa</button> }
                                        <button class="btn btn-sm btn-primary btn-margin" onClick={this.deleteQuota(q.version)}>Xóa</button>
                                    </td> }
                                </tr>
                            }), <tr>
                                <th>Hệ số GVHD chính</th>
                                <td>{q.mainFactor[0]}</td>
                                <td>{q.mainFactor[1]}</td>
                                <td>{q.mainFactor[2]}</td>
                            </tr>, <tr>
                                <th>Hệ số GV đồng HD</th>
                                <td>{q.coFactor[0]}</td>
                                <td>{q.coFactor[1]}</td>
                                <td>{q.coFactor[2]}</td>
                            </tr>]
                        }) }
                    </tbody>
                </table>
            </div>
            { degrees.isLoaded && <QuotasCreate
                actions={actions}
                degrees={degrees.list}
                reloadData={this.reloadData}
            /> }
            { degrees.isLoaded && <QuotasUpdate
                actions={actions}
                degrees={degrees.list}
                changeQuota={changeQuota}
                reloadData={this.reloadData}
            /> }
        </div>
    }
}

export default SuperuserQuotas
