```markdown
# Database 기반 Q&A 커뮤니티 프로젝트

코딩 중 막힌 부분을 빠르게 질문하고, 같은 오류를 겪었던 사람들의 코드를 참고할 수 있는 “Q&A 커뮤니티” 웹 서비스를 구축한 프로젝트입니다. Stack Overflow와 유사하지만, **국내 사용자에게 맞춘 환경**을 제공하기 위해 제작되었습니다. 다음과 같은 핵심 기능을 포함하고 있습니다.

---
## 개발 환경 및 버전
- **OS**: Windows 10 / Ubuntu 22.04 등  
- **웹 서버**: Apache 2.4+  
- **PHP**: 7.4+ 또는 8.0+  
- **DB**: MySQL 5.7+ 또는 MariaDB 10+  
- **브라우저**: Chrome

## 목차
1. [프로젝트 개요](#프로젝트-개요)  
2. [핵심 기능](#핵심-기능)  
3. [시스템 아키텍처](#시스템-아키텍처)  
4. [ER 다이어그램](#er-다이어그램)  
5. [데이터베이스 구조](#데이터베이스-구조)  
   - [정규화 (3NF)](#정규화-3nf)  
6. [주요 테이블](#주요-테이블)  
7. [웹 구현 및 주요 코드 설명](#웹-구현-및-주요-코드-설명)  
8. [설치 및 실행 방법](#설치-및-실행-방법)  
9. [개발 환경 및 버전](#개발-환경-및-버전)  
10. [Git 관리](#git-관리)  
11. [라이선스](#라이선스)  

---

## 프로젝트 개요
- **프로젝트 목적**  
  코딩 중 발생하는 오류나 막힌 부분을 빠르게 공유하고 도움을 받을 수 있는 커뮤니티를 구축하고자 했습니다.  
- **프로젝트 선정 이유**  
  - 기존에 유명한 해외 사이트(예: Stack Overflow)는 언어 장벽이 있는 경우가 많음  
  - 한국어 기반으로 좀 더 편리하고 빠르게 Q&A를 할 수 있는 사이트를 구현  
- **주요 특징**  
  - 사용자 인증(회원가입/로그인)  
  - 게시글(질문) 작성과 댓글(답변) 작성  
  - 코드 언어(Tag) 분류, 검색 및 필터링  
  - 좋아요(추천) 기능을 통한 답변 품질 관리  
  - PHP와 MySQL로 구성  

---

## 핵심 기능
1. **사용자 계정 및 인증**  
   - 회원가입, 로그인 구현  
   - 세션(Session)으로 로그인 상태 유지  
   - 비밀번호는 해시/암호화 처리

2. **게시물 관리**  
   - 새 게시물(질문) 작성, 수정, 삭제  
   - 게시물에는 문제 설명, 예시 코드, 언어 태그 등 포함  

3. **댓글(답변) 작성 및 관리**  
   - 게시물에 댓글 달기  
   - 댓글 좋아요, 댓글 삭제 기능  

4. **태그 및 검색**  
   - 게시물에 코드 언어(Tag)를 달아서 분류  
   - 특정 태그(언어)로 게시물 필터링  
   - 텍스트 검색 기능  

5. **좋아요 시스템**  
   - 댓글의 유용성을 평가할 수 있도록 좋아요 버튼 제공  
   - 좋아요 수에 따른 답변 품질 판단  

6. **로그인 기록 관리**  
   - 사용자가 로그인할 때마다 접속 날짜/시간 기록  
   - 관리자(또는 운영자)가 로그인 이력 확인 가능  

7. **보안 및 데이터 관리**  
   - 비밀번호 해시(암호화) 저장  
   - SQL Injection 방지(Prepared Statement), XSS 방어(HTML 이스케이프)  

---

## 시스템 아키텍처

```
[웹 브라우저]
      |
      v
[Apache/PHP 서버] -- [PHP 파일들] 
      |
      v
[MySQL DB 서버]
```

- **PHP**로 작성된 서버 사이드 로직이 **MySQL**과 연동  
- **Apache/PHP** 환경에서 웹 페이지를 사용자에게 제공  

---

## ER 다이어그램

프로젝트 내에서 정의된 **실제 ER 다이어그램**은 아래와 같은 엔티티와 관계를 담고 있습니다:

- **Users** (사용자 정보)  
- **Posts** (게시물 정보)  
- **Comments** (댓글 정보)  
- **Likes** (좋아요 정보)  
- **LoginLogs** (로그인 기록)

각 엔티티 간 관계(개념 예시):

```
Users --< Posts
Posts --< Comments
Users --< Comments
Comments --< Likes
Users --< LoginLogs
```

---

## 데이터베이스 구조

### 1) 테이블 개요
- **Users**  
  - 사용자 계정 정보(아이디, 비밀번호, 사용자명 등)  
  - 비밀번호는 해시 처리 후 저장  

- **Posts**  
  - 게시물(질문) 정보(제목, 내용, 코드 언어 태그, 작성 날짜 등)  
  - 작성자(Users)와 1:N 관계  

- **Comments**  
  - 댓글(답변) 정보(내용, 작성 날짜, 좋아요 수 등)  
  - 게시물(Posts)과 1:N, 작성자(Users)와 1:N 관계  

- **Likes**  
  - 댓글에 좋아요를 누른 기록 저장(CommentID, UserID)  
  - 같은 댓글에 대해 한 유저가 좋아요를 중복으로 누르지 못하도록 설계  

- **LoginLogs**  
  - 사용자가 로그인할 때마다 접속한 날짜와 시간 기록  
  - UserID와 N:1 관계 (여러 번 접속 가능)

### 2) 정규화 (3NF)
프로젝트에서는 **3차 정규형(3NF)**를 만족하도록 스키마를 구성했습니다.

- **기본키(Primary Key)**를 통해 모든 속성이 종속되도록 설계  
- **부분 함수 종속**, **이행적 종속** 제거  
- 예) `Users` 테이블은 `UserID`만 Primary Key로 설정하고, `Password` 등은 `UserID`에만 종속  

데이터 중복 최소화와 무결성 확보를 위해 테이블을 필요한 만큼 세분화하였습니다.

---

## 주요 테이블

아래는 일부 테이블 생성 예시입니다(핵심 컬럼 중심):

#### 1. **Users 테이블**

```sql
CREATE TABLE Users (
    UserID       VARCHAR(50) PRIMARY KEY,
    Username     VARCHAR(50) NOT NULL,
    Password     VARCHAR(255) NOT NULL,
    CreatedAt    DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
- `UserID`: 유저의 고유 ID (아이디)  
- `Username`: 사용자명  
- `Password`: 해시된 비밀번호  
- `CreatedAt`: 계정 생성 시각  

#### 2. **Posts 테이블**

```sql
CREATE TABLE Posts (
    PostID        INT AUTO_INCREMENT PRIMARY KEY,
    UserID        VARCHAR(50) NOT NULL,
    Title         VARCHAR(255) NOT NULL,
    Content       TEXT NOT NULL,
    CodeLanguage  VARCHAR(50),
    CreatedAt     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
```
- `PostID`: 게시물 고유 번호  
- `UserID`: 작성자 (Users 참조)  
- `Title`: 게시물 제목  
- `Content`: 게시물 내용(문제 설명, 코드 등)  
- `CodeLanguage`: 사용 언어 태그 (예: C, Python, JavaScript 등)  

#### 3. **Comments 테이블**

```sql
CREATE TABLE Comments (
    CommentID     INT AUTO_INCREMENT PRIMARY KEY,
    PostID        INT NOT NULL,
    UserID        VARCHAR(50) NOT NULL,
    Content       TEXT NOT NULL,
    Likes         INT DEFAULT 0,
    CreatedAt     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PostID) REFERENCES Posts(PostID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
```
- `CommentID`: 댓글 고유 번호  
- `PostID`: 어느 게시물에 속하는지 (Posts 참조)  
- `UserID`: 댓글 작성자  
- `Likes`: 해당 댓글의 좋아요 수  

#### 4. **Likes 테이블**

```sql
CREATE TABLE Likes (
    LikeID      INT AUTO_INCREMENT PRIMARY KEY,
    CommentID   INT NOT NULL,
    UserID      VARCHAR(50) NOT NULL,
    FOREIGN KEY (CommentID) REFERENCES Comments(CommentID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
```
- `LikeID`: 좋아요 고유 번호  
- `CommentID`: 어떤 댓글에 대한 좋아요인지  
- `UserID`: 좋아요를 누른 사용자  

#### 5. **LoginLogs 테이블**

```sql
CREATE TABLE LoginLogs (
    LogID       INT AUTO_INCREMENT PRIMARY KEY,
    UserID      VARCHAR(50) NOT NULL,
    LoggedAt    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
```
- `LogID`: 고유 로그 번호  
- `UserID`: 로그인한 사용자  
- `LoggedAt`: 로그인 시간  

---

## 웹 구현 및 주요 코드 설명

1. **메인 페이지(Index)**
   - 전체 게시물 리스트 표시  
   - 최신순 정렬(`ORDER BY CreatedAt DESC`)  
   - 언어 태그나 검색어에 따라 필터 적용

2. **로그인 / 회원가입**
   - ```sql
     SELECT * FROM Users WHERE UserID = ?
     ```
     - 로그인 시 유효한 아이디/비밀번호인지 확인  
   - ```sql
     INSERT INTO Users (UserID, Username, Password) VALUES (?, ?, ?)
     ```
     - 회원가입 시 아이디 중복 검사 후 저장  
     - 비밀번호는 해시 함수 사용  

3. **게시물 작성**
   - ```sql
     INSERT INTO Posts (UserID, Title, Content, CodeLanguage) VALUES (?, ?, ?, ?)
     ```
   - 로그인한 사용자만 작성 가능  
   - 제목, 내용, 언어 태그 등을 함께 저장  

4. **게시물 상세 페이지**
   - ```sql
     SELECT * FROM Posts WHERE PostID = ?
     ```
   - 게시물 및 댓글 정보를 함께 로드  
   - 내용, 작성 날짜, 언어 등 표시  

5. **댓글 작성/삭제**
   - **작성**: ```sql
     INSERT INTO Comments (PostID, UserID, Content) VALUES (?, ?, ?)
     ```
   - **삭제**: ```sql
     DELETE FROM Comments WHERE CommentID = ?
     ```
     - 작성자 본인만 삭제 가능  

6. **좋아요 시스템**
   - 댓글에 좋아요 누르기 전, DB(Likes 테이블)에서 중복 여부 체크  
   - 없으면
     ```sql
     INSERT INTO Likes (CommentID, UserID) VALUES (?, ?)
     ```
   - 이미 있으면
     ```sql
     DELETE FROM Likes WHERE CommentID = ? AND UserID = ?
     ```
   - ```sql
     UPDATE Comments SET Likes = ? WHERE CommentID = ?
     ```
     - 댓글 좋아요 수 실시간 반영  

7. **로그인 로그**
   - 로그인 성공 시
     ```sql
     INSERT INTO LoginLogs (UserID) VALUES (?)
     ```
   - 누가 언제 접속했는지 기록  

---

## 설치 및 실행 방법

1. **환경 준비**
   - **Apache**, **PHP**, **MySQL**이 동작하는 서버 환경  
   - XAMPP, LAMP, MAMP 등 통합 패키지 사용 가능  

2. **소스코드 다운로드**
   - GitHub 레포지토리에서 프로젝트 클론
     ```bash
     git clone https://github.com/robot703/DataBaseTermProject-PHP-.git
     ```

3. **데이터베이스 설정**
   - MySQL 접속 후 Database 생성
     ```sql
     CREATE DATABASE CommunityPlatform;
     USE CommunityPlatform;
     ```
   - 필요한 테이블 생성(DDL 스크립트 실행)

4. **설정 파일 수정**
   - DB 접속 정보를 `config.php`(또는 유사 파일)에서 설정
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "비밀번호";
     $dbname = "CommunityPlatform";
     ```

5. **웹 서버 구동**
   - Apache/PHP 서버 실행  
   - 브라우저에서 `http://localhost/index.php` 접속  

6. **회원가입 후 로그인**
   - 회원가입 페이지에서 사용자 생성  
   - 로그인 후 게시물/댓글 작성 테스트  

---


