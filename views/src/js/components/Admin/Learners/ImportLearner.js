import React, { Component } from 'react'
import { notify } from 'Components'
import { mailerActions } from 'Actions';

const learnerInfo = [
    "STT",
    "Tên tài khoản",
    "Mật khẩu",
    "Mã học viên",
    "Tên học viên",
    "VNU email",
    "Khóa đào tạo",
]

class AdminImportLearnerExcel extends Component {
    constructor(props) {
        super(props);

        this.state = {
            loadding: false,
            error : false,
            message: '',
            showDemo: false,
            parsedData: [],
            unparsedData: [],   //data which are invalid to be updated to Database
            invalidData: []     //data which cannot be updated to Database
        };

        this.handleSheet = this.handleSheet.bind(this);
        this.handleSheetData = this.handleSheetData.bind(this);
        this.handleFile = this.handleFile.bind(this);
        this.resetForm = this.resetForm.bind(this);
        this.renderParsedData = this.renderParsedData.bind(this);
    }

    reloadData() {
        const { actions, modalId } = this.props
        actions.loadLearners().then(() => {
            this.resetForm();
            $(`#${modalId}`).modal('hide');
            notify.show('Cập nhật danh sách học viên thành công', 'primary');
        })
    }

    /*
    - Reset importing learner modal
    */
    resetForm(e) {
        document.getElementById('adminImportLearnerExcelForm').reset();

        this.setState({
            loadding: false,
            error : false,
            message: '',
            showDemo: false,
            parsedData: [],
            unparsedData: [],
            invalidData: []
        })
    }

    /*
    - Handle submit data
    */
    handleSubmit(e) {
        e.preventDefault();
        const { actions } = this.props;
        const { parsedData, unparsedData, invalidData } = this.state;
        let errorData = []

        if(parsedData.length > 0) {
            this.setState({
                loadding: true,
                showDemo: false,
            });

            //Add learners to database
            actions.importLearner(parsedData).then(response => {
                //get all learners which have invalid data
                response.action.payload.data.forEach((item, id) => {
                    if(item.error){
                        errorData.push({...parsedData[id], error: item.error})
                    }
                });
                if(errorData.length <= 0 && unparsedData.length <= 0) {  //no error
                    this.reloadData();
                } else {    //handle to show error
                    actions.loadLearners();
                    notify.show('Đã cập nhật danh sách học viên', 'primary');
                    this.setState({
                        invalidData: errorData,
                        loadding: false
                    });
                }
                mailerActions.setPasswordMail();
            }).catch(err => {
                if(!err || !err.response) {
                    this.setState({
                        error: false,
                        loadding: false
                    })
                } else {
                    this.setState({
                        error: true,
                        message: err.response.data.message
                    })
                }
            })
        }
    }

    /*
    - Render demo table from parsed excel data
    */
    renderParsedData() {
        const { parsedData } = this.state;

        const div = [];

        for(let i = 0; i < 10; i++){
            let learner = parsedData[i];

            if(!learner) { break; }
            else {
                div.push(
                    <tr key={learner.learnerCode + learner.username} class='row-item'>
                        <td>{learner.username}</td>
                        <td>{learner.password}</td>
                        <td>{learner.learnerCode}</td>
                        <td>{learner.fullname}</td>
                        <td class="hidden-xs hidden-sm">{learner.vnuMail}</td>
                    </tr>
                );
            }
        }

        return ( <tbody>{div}</tbody> )
    }

    /*
    - Handle loaded file
    */
    handleFile(e) {
        var files = e.target.files;
        var excelHandling = this;

        this.setState({
            loadding: true,
            error : false,
            message: '',
            showDemo: false,
            parsedData: [],
            unparsedData: []
        })

        for (let i = 0, f = files[i]; i != files.length; ++i) {
            var reader = new FileReader();
            var name = f.name;

            /* Check file type */
            let extentions = name.split('.');
            let validTypeFile = false;
            extentions = extentions[extentions.length - 1];

            //console.log(extentions);

            switch(extentions)
            {
                case 'xls':
                case 'xlsx':
                    validTypeFile = true;
                    break;
                default:
                    validTypeFile = false;
                    break;
            }

            if(validTypeFile) {
                excelHandling.setState({
                    error: false,
                    message: ''
                })

                reader.onload = function(e) {
                    var data = e.target.result;

                    var workbook = XLSX.read(data, {type: 'binary'});

                    //handle first Sheet
                    excelHandling.handleSheet(workbook, extentions);
                };

                reader.readAsBinaryString(f);
            }

            else {
                excelHandling.setState({
                    loadding: false,
                    error: true,
                    message: 'Định dạng tệp yêu cầu là .xls hoặc .xlsx'
                })
                break;
            }
        }
    }

    /* CONDITIONS
    - only parse specificed fields
    - show error message when:
        + invalid file format (not .xlsx or .xls)
        + required fields of a row is undefined
        + invalid field header name
    */
    handleSheet(workbook, extentions){
        var sheetJS = ( extentions === 'xls' ? XLS : XLSX );

        for(var id in workbook.SheetNames) {
            var workSheetName = workbook.SheetNames[id];
            var worksheet = workbook.Sheets[workSheetName];

            var headers = [];
            var range = null;

            if(worksheet['!ref']){
                range = sheetJS.utils.decode_range(worksheet['!ref']);
            } else {
                continue;
            }
            
            if(range) {
                var C, R = range.s.r; /* start in the first row */
            
                /* walk every column in the range */
                for(C = range.s.c; C <= range.e.c; ++C) {
                    var cell = worksheet[sheetJS.utils.encode_cell({c:C, r:R})] /* find the cell in the first row */

                    if(cell && cell.t) {
                        var hdr = sheetJS.utils.format_cell(cell);
                        headers.push(hdr);
                    }
                }
            } else {
                continue;
            }

            /*-----
                Check required field header
            -----*/
            var validFields = false;
            for (let i in learnerInfo){
                if(headers.includes(learnerInfo[i])){
                    validFields = true;
                } else {
                    this.setState({
                        loadding: false,
                        error: true,
                        message: 'Thiếu hoặc sai tên trường: ' + learnerInfo[i]
                    })

                    validFields = false;
                    break;
                }
            }

            if(validFields) {
                this.setState({
                    error: false,
                    message: ''
                })

                this.handleSheetData(worksheet, sheetJS);
            } else {
                return;
            }
        }
    }

    /* CONDITIONS
    - all required field must have valid value (not null)
    */
    handleSheetData(worksheet, sheetJS) {
        const { courses } = this.props;
        var data = sheetJS.utils.sheet_to_row_object_array(worksheet);

        var { parsedData, unparsedData } = this.state;

        for(let learnerId in data){
            var learner = {};
            let validInfo = true;

            //Save valid data (not undefined)

            if(data[learnerId][learnerInfo[1]]) {   //username
                learner.username = data[learnerId][learnerInfo[1]].trim();

                if(data[learnerId][learnerInfo[2]]) {   //password
                    learner.password = data[learnerId][learnerInfo[2]];

                    if(data[learnerId][learnerInfo[3]]) {   //learnerCode
                        learner.learnerCode = data[learnerId][learnerInfo[3]].trim();

                        if(data[learnerId][learnerInfo[4]]) {   //fullname
                            learner.fullname = data[learnerId][learnerInfo[4]].trim();

                            if(data[learnerId][learnerInfo[5]]) {   //vnuMail
                                learner.vnuMail = data[learnerId][learnerInfo[5]].trim();

                                if(data[learnerId][learnerInfo[6]]) {   //course
                                    let courseCode = data[learnerId][learnerInfo[6]].trim();
                                    let courseInfo = courses.list.filter( (acourse) => acourse.courseCode === courseCode );

                                    if(courseInfo.length > 0) {
                                        learner.trainingCourseId = courseInfo[0].id;
                                    }
                                    else {
                                        validInfo = false;
                                        unparsedData.push(data[learnerId][learnerInfo[3]]);
                                        continue
                                    }
                                }
                                else {
                                    validInfo = false;
                                    unparsedData.push({id: data[learnerId][learnerInfo[3]], error: "Khóa học không tồn tại"});
                                    continue
                                }
                            }
                            else {
                                validInfo = false;
                                unparsedData.push({id: data[learnerId][learnerInfo[3]], error: "Địa chỉ VNU mail không hợp lệ"});
                                continue
                            }
                        }
                        else {
                            validInfo = false;
                            unparsedData.push({id: data[learnerId][learnerInfo[3]], error: "Tên người dùng không lệ"});
                            continue
                        }
                    }
                    else {
                        validInfo = false;
                        unparsedData.push({id: data[learnerId][learnerInfo[1]], error: "Mã học viên không hợp lệ"});
                        continue
                    }
                }
                else {
                    validInfo = false;
                    unparsedData.push({id: data[learnerId][learnerInfo[3]], error: "Mật khẩu không hợp lệ"});
                    continue
                }
            }
            else {
                validInfo = false;
                unparsedData.push({id: data[learnerId][learnerInfo[3]], error: "Tên tài khoản không hợp lệ"});
                continue
            }

            if(validInfo) { parsedData.push(learner); }
        }

        if(parsedData.length === 0) {
            this.setState({
                showDemo: false,
                loadding: false,
                error: true,
                message: 'Đọc dữ liệu không thành công.'
            });
        } else {
            this.setState({
                showDemo: true,
                loadding: false,
                error: false,
                message: ''
            });
        }

        this.setState({
            parsedData: parsedData,
            unparsedData: unparsedData
        });
    }


    render() {
        const { modalId } = this.props;
        const { loadding, error, message } = this.state;
        const { showDemo, parsedData, unparsedData, invalidData } = this.state;

        const downloadFileStyle = {
            textDecoration: 'none',
            color: '#000'
        };

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="leanerExcelModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick={(e) => this.resetForm(e)}>
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="leanerExcelModal">Thêm danh sách từ file excel (.xlsx)</h4>
                        </div>
                        <form class="form-horizontal" id="adminImportLearnerExcelForm" onSubmit={(e) => this.handleSubmit(e)}>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Chọn tệp excel: </label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control" id="adminImportLearnerExcelInput"
                                         accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                         required
                                         onClick = {(e) => e.target.value = null}
                                         onChange = {(e) => this.handleFile(e)}/>
                                    </div>
                                </div>

                                { loadding &&
                                    <div class="process-load-spinner">
                                        <img src = "/images/load-spinner.gif" />
                                    </div>
                                }

                                { !loadding && error &&
                                    <div>
                                        <div class="col-sm-offset-1 text-danger">Có lỗi xảy ra: {message}</div>
                                        <br />
                                    </div>
                                }

                                { !loadding && showDemo &&
                                    <div class="table-responsive">
                                        <table class="table table-hover table-condensed">
                                            <caption><b>Danh sách mẫu: </b></caption>
                                            <thead>
                                                <tr>
                                                    <th class="col-xs-2 col-sm-2">Tên người dùng</th>
                                                    <th class="col-xs-3 col-sm-3 col-md-2">Mật khẩu</th>
                                                    <th class="col-xs-2 col-sm-2">Mã học viên</th>
                                                    <th class="col-xs-3 col-sm-3 col-md-2">Tên học viên</th>
                                                    <th class="hidden-xs hidden-sm col-md-3">VNU email</th>
                                                </tr>
                                            </thead>

                                            { this.renderParsedData() }

                                        </table>
                                    </div>
                                }

                                { !loadding && invalidData.length > 0 &&
                                    <div>
                                        <span>Danh sách các mã học viên chưa thêm thành công: </span>
                                        <div class="text-message-warning error-wrapper-scrolling">
                                            {
                                                invalidData.map(learner => {
                                                    return (
                                                        <div key={learner.username + learner.learnerCode}>{learner.learnerCode}: {learner.error}</div>
                                                    )
                                                })
                                            }
                                        </div>
                                    </div>
                                }

                                { !loadding && unparsedData.length > 0 &&
                                <div>
                                    <span>Danh sách mã học viên đọc không thành công (từ file Excel): </span>
                                    <div class="text-message-warning">
                                        <div class="text-message-warning error-wrapper-scrolling">
                                            {
                                                unparsedData.map(learner => {
                                                    return (
                                                        <div key={learner.id + '-excel'}>{learner.id}: {learner.error} </div>
                                                    )
                                                })
                                            }
                                        </div>
                                    </div>
                                </div>
                                }
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" onClick={() => window.open('/download/dshocvien.zip')}>
                                    Tải tệp mẫu
                                </button>
                                <button type="submit" class="btn btn-primary">Đồng ý</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal"
                                    onClick={(e) => this.resetForm(e)}>Bỏ qua</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default AdminImportLearnerExcel
