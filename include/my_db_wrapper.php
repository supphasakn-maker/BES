<?php

class MyDbWrapper
{
    private $mysqli_connection; // เก็บ object mysqli จริงๆ

    public function __construct($host, $user, $pass, $db_name)
    {
        $this->mysqli_connection = new mysqli($host, $user, $pass, $db_name);

        if ($this->mysqli_connection->connect_error) {
            // บันทึก error แทนที่จะ die() ตรงๆ ใน production
            error_log("MyDbWrapper: Database connection failed: " . $this->mysqli_connection->connect_error);
            die("Database connection failed. Please try again later."); // หยุดการทำงานถ้าเชื่อมต่อไม่ได้
        }
        $this->mysqli_connection->set_charset("utf8"); // ตั้งค่า Charset
    }

    /**
     * ตรวจสอบว่ามีระเบียน (record) ตรงตามเงื่อนไขใน SQL Query หรือไม่
     * เมธอดนี้จะถูกเรียกใช้โดย oceanos.php
     * @param string $sql_query SQL Query ที่ใช้ SELECT COUNT(*)
     * @param string $param_types (optional) ประเภทของพารามิเตอร์ (เช่น "s" สำหรับ string, "i" สำหรับ integer)
     * @param mixed ...$params (optional) พารามิเตอร์ที่จะ bind
     * @return bool true ถ้ามีระเบียนอย่างน้อย 1 ระเบียน, false ถ้าไม่มี
     */
    public function HasRecord($sql_query, $param_types = '', ...$params)
    {
        $stmt = $this->mysqli_connection->prepare($sql_query);
        if (!$stmt) {
            error_log("MyDbWrapper HasRecord: Failed to prepare statement: " . $this->mysqli_connection->error . " SQL: " . $sql_query);
            return false;
        }

        if (!empty($param_types) && !empty($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            error_log("MyDbWrapper HasRecord: Failed to get result: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $row = $result->fetch_array(MYSQLI_NUM); // ดึงผลลัพธ์เป็น array ตัวเลข
        $stmt->close();

        // ตรวจสอบว่ามีแถวและค่าในคอลัมน์แรกมากกว่า 0 หรือไม่
        return (isset($row[0]) && $row[0] > 0);
    }


    public function query($sql)
    {
        return $this->mysqli_connection->query($sql);
    }

    public function prepare($sql)
    {
        return $this->mysqli_connection->prepare($sql);
    }

    public function real_escape_string($string)
    {
        return $this->mysqli_connection->real_escape_string($string);
    }

    public function get_error()
    {
        return $this->mysqli_connection->error;
    }


    public function __destruct()
    {
        if ($this->mysqli_connection) {
            $this->mysqli_connection->close();
        }
    }
}
