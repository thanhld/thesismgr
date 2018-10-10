import React, { Component } from 'react'
import { notify } from 'Components'
import { mailerActions } from 'Actions';

const officerInfo = [
    "STT",                  //0
    "Tên tài khoản",        //1
    "Mật khẩu",             //2
    "Mã cán bộ",            //3
    "Tên cán bộ",           //4
    "VNU email",             //5
    "Cán bộ",               //6
    "Học hàm, học vị",      //7
    "Đơn vị công tác"       //8
]

const officerTypes = [
    {
        'label': 'Giảng viên',
        'value': 3
    },
    {
        'label': 'Chuyên viên',
        'value': 4
    },
    {
        'label': 'Giảng viên ngoài',
        'value': 5
    },
    {
        'label': 'Cán bộ ngoài',
        'value': 5
    }
]

class AdminImportOfficerExcel extends Component {
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
        actions.loadOfficers().then(() => {
            this.resetForm();
            $(`#${modalId}`).modal('hide');
            notify.show('Cập nhật danh sách cán bộ thành công', 'primary');
        })
    }

    /*
    - Reset importing officer modal
    */
    resetForm(e) {
        document.getElementById('adminImportOfficerExcelForm').reset();

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
        var errorData = []

        if(parsedData.length > 0) {
            this.setState({
                loadding: true,
                showDemo: false,
            });

            //Add officers to database
            actions.importOfficer(parsedData).then(response => {
                //get all officers which have invalid data
                response.action.payload.data.forEach((item, id) => {
                    if(item.error){
                        errorData.push({...parsedData[id], error: item.error})
                    }
                });
                if(errorData.length <= 0 && unparsedData.length <= 0) {  //no error
                    this.reloadData();
                } else {    //handle to show error
                    actions.loadOfficers();
                    notify.show('Đã cập nhật danh sách giảng viên', 'primary');
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

        for(var i = 0; i < 10; i++){
            var officer = parsedData[i];

            if(!officer) { break; }
            else {
                div.push(
                    <tr key={officer.officerCode + officer.username} class='row-item'>
                        <td>{officer.username}</td>
                        <td>{officer.password}</td>
                        <td>{officer.officerCode}</td>
                        <td>{officer.fullname}</td>
                        <td class="hidden-xs hidden-sm">{officer.vnuMail}</td>
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

        for (var i = 0, f = files[i]; i != files.length; ++i) {
            var reader = new FileReader();
            var name = f.name;

            /* Check file type */
            var extentions = name.split('.');
            var validTypeFile = false;
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

        /* Get worksheet */
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

            //console.log(headers);

            /*-----
                Check required field header
            -----*/
            var validFields = false;
            for (var i in officerInfo){
                if(headers.includes(officerInfo[i])){
                    validFields = true;
                } else {
                    this.setState({
                        loadding: false,
                        error: true,
                        message: "Thiếu hoặc sai tên trường: " + officerInfo[i]
                    });

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
        const { degrees, departments } = this.props;
        var data = sheetJS.utils.sheet_to_row_object_array(worksheet);

        var { parsedData, unparsedData } = this.state;

        for(var officerId in data){
            var officer = {};
            var validInfo = true;

            //Save valid data (not undefined)

            if(data[officerId][officerInfo[1]]) {   //username
                officer.username = data[officerId][officerInfo[1]].trim();

                if(data[officerId][officerInfo[2]]) {   //password
                    officer.password = data[officerId][officerInfo[2]];

                    if(data[officerId][officerInfo[3]]) {   //officerCode
                        officer.officerCode = data[officerId][officerInfo[3]].trim();

                        if(data[officerId][officerInfo[4]]) {   //fullname
                            officer.fullname = data[officerId][officerInfo[4]].trim();

                            if(data[officerId][officerInfo[5]]) {   //vnuMail
                                officer.vnuMail = data[officerId][officerInfo[5]].trim();

                                if(data[officerId][officerInfo[6]]) {   //officerType
                                    var officerType = data[officerId][officerInfo[6]].trim();
                                    var type = officerTypes.filter( (type) => type.label.toLowerCase() == officerType.toLowerCase() );

                                    if(type.length > 0) {
                                        officer.role = type[0].value;

                                        if(data[officerId][officerInfo[8]]) {  //department
                                            var officerDepartment = data[officerId][officerInfo[8]].trim();
                                            var department = departments.list.filter( (depart) => depart.name.toLowerCase() == officerDepartment.toLowerCase() );
                                            if(department.length > 0) {
                                                officer.departmentId = department[0].id;
                                            } else {
                                                validInfo = false;
                                                unparsedData.push({id: data[officerId][officerInfo[3]], error: "Đơn vị công tác không tồn tại"});
                                                continue;
                                            }
                                        }
                                        else {
                                            var department = departments.list.filter((d) => d.type == 4); // VPK type
                                            if (department.length > 0) {
                                                officer.departmentId = department[0].id;
                                            } else {
                                                validInfo = false;
                                                unparsedData.push({id: data[officerId][officerInfo[3]], error: "Văn phòng khoa không tồn tại"});
                                                continue
                                            }
                                        }
                                    }
                                    else {
                                        validInfo = false;
                                        unparsedData.push({id: data[officerId][officerInfo[3]], error: "Loại cán bộ không hợp lệ"});
                                        continue;
                                    }
                                }
                                else {
                                    validInfo = false;
                                    unparsedData.push({id: data[officerId][officerInfo[3]], error: "Loại cán bộ không hợp lệ"});
                                    continue
                                }
                            }
                            else {
                                validInfo = false;
                                unparsedData.push({id: data[officerId][officerInfo[3]], error: "Địa chỉ vnuMail không hợp lệ"});
                                continue
                            }
                        }
                        else {
                            validInfo = false;
                            unparsedData.push({id: data[officerId][officerInfo[3]], error: "Tên riêng người dùng không hợp lệ"});
                            continue
                        }
                    }
                    else {
                        validInfo = false;
                        unparsedData.push({id: data[officerId][officerInfo[1]], error: "Mã cán bộ không hợp lệ"});
                        continue
                    }
                }
                else {
                    validInfo = false;
                    unparsedData.push({id: data[officerId][officerInfo[3]], error: "Mật khẩu người dùng không hợp lệ"});
                    continue
                }
            }
            else {
                validInfo = false;
                unparsedData.push({id: data[officerId][officerInfo[3]], error: "Tên tài khoản người dùng không hợp lệ"});
                continue;
            }

            //degree - not required
            if(data[officerId][officerInfo[7]]) {
                var officerDegree = data[officerId][officerInfo[7]];
                var degree = degrees.list.filter( (degree) => degree.name.toLowerCase() == officerDegree.toLowerCase() );
                if(degree.length > 0) {
                    officer.degreeId = degree[0].id;
                }
                else {
                    validInfo = false;
                    unparsedData.push({id: data[officerId][officerInfo[3]], error: "Học hàm, học vị cán bộ không hợp lệ"});
                }
            }

            if(validInfo) { parsedData.push(officer); }
        }

        if(parsedData.length == 0) {
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
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="officerExcelModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick={(e) => this.resetForm(e)}>
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="officerExcelModal">Thêm danh sách từ file excel (.xlsx)</h4>
                        </div>
                        <form class="form-horizontal" id="adminImportOfficerExcelForm" onSubmit={(e) => this.handleSubmit(e)}>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Chọn tệp excel: </label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control" id="adminImportOfficerExcelInput"
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
                                                    <th class="col-xs-2 col-sm-2">Mã cán bộ</th>
                                                    <th class="col-xs-3 col-sm-3 col-md-2">Tên cán bộ</th>
                                                    <th class="hidden-xs hidden-sm col-md-3">VNU email</th>
                                                </tr>
                                            </thead>

                                            { this.renderParsedData() }

                                        </table>
                                    </div>
                                }

                                { !loadding && invalidData.length > 0 &&
                                <div>
                                    <span>Danh sách các mã cán bộ chưa thêm thành công: </span>
                                    <div class="text-message-warning">
                                        <div class="text-message-warning error-wrapper-scrolling">
                                            {
                                                invalidData.map(officer => {
                                                    return (
                                                        <div key={officer.username + officer.officerCode}>{officer.username}: {officer.error}</div>
                                                    )
                                                })
                                            }
                                        </div>
                                    </div>
                                </div>
                                }

                                { !loadding && unparsedData.length > 0 &&
                                <div>
                                    <span>Danh sách mã cán bộ đọc không thành công (từ file Excel): </span>
                                    <div class="text-message-warning">
                                        <div class="text-message-warning error-wrapper-scrolling">
                                            {
                                                unparsedData.map(officer => {
                                                    return (
                                                        <div key={officer.id + '-excel'}>{officer.id}: {officer.error}</div>
                                                    )
                                                })
                                            }
                                        </div>
                                    </div>
                                </div>
                                }
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" onClick={() => window.open('/download/dscanbo.zip')}>
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

export default AdminImportOfficerExcel
