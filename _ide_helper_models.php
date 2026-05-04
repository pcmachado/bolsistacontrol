<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $project_id
 * @property int|null $course_id
 * @property int|null $class_offering_id
 * @property int|null $unit_id
 * @property string $assignment_type
 * @property int|null $position_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Project|null $project
 * @property-read \App\Models\Unit|null $unit
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereAssignmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereUserId($value)
 */
	class Assignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scholarship_holder_id
 * @property int|null $attendance_submission_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property numeric $hours
 * @property string|null $description
 * @property int $has_issue
 * @property string|null $issue_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $computed_status
 * @property-read mixed $month
 * @property-read string $status_label
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @property-read \App\Models\AttendanceSubmission|null $submission
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord byMonth(int $month, int $year)
 * @method static \Database\Factories\AttendanceRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereAttendanceSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereHasIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereIssueReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereScholarshipHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord withoutTrashed()
 */
	class AttendanceRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scholarship_holder_id
 * @property int $month
 * @property int $year
 * @property numeric|null $total_hours
 * @property numeric|null $calculated_value
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $approved_by
 * @property string|null $rejected_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceRecord> $attendanceRecords
 * @property-read int|null $attendance_records_count
 * @property-read string $period
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission forMonth(int $month, int $year)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereCalculatedValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereScholarshipHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSubmission withoutTrashed()
 */
	class AttendanceSubmission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property int $unit_id
 * @property int|null $project_id
 * @property string|null $name
 * @property string|null $semester
 * @property string|null $year
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $capacity
 * @property int $active
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOfferingSubmission> $classOfferingSubmissions
 * @property-read int|null $class_offering_submissions_count
 * @property-read \App\Models\Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Discipline> $disciplines
 * @property-read int|null $disciplines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentMonthRecord> $monthRecords
 * @property-read int|null $month_records_count
 * @property-read \App\Models\Project|null $project
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScholarshipHolder> $scholarshipHolders
 * @property-read int|null $scholarship_holders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSession> $sessions
 * @property-read int|null $sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentRecord> $studentRecords
 * @property-read int|null $student_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOfferingSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \App\Models\Unit|null $unit
 * @method static \Database\Factories\ClassOfferingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOffering withoutTrashed()
 */
	class ClassOffering extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $class_offering_id
 * @property int $discipline_id
 * @property int|null $teacher_id
 * @property int|null $workload
 * @property string|null $schedule
 * @property string|null $room
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Discipline|null $discipline
 * @property-read \App\Models\User|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline whereWorkload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingDiscipline withoutTrashed()
 */
	class ClassOfferingDiscipline extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingStudent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingStudent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingStudent query()
 */
	class ClassOfferingStudent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $class_offering_id
 * @property int $total_students
 * @property numeric $total_amount
 * @property string $status
 * @property string|null $submitted_at
 * @property string|null $approved_at
 * @property string|null $rejected_at
 * @property string|null $rejected_reason
 * @property int $month
 * @property int $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission forMonth(int $month, int $year)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereTotalStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassOfferingSubmission withoutTrashed()
 */
	class ClassOfferingSubmission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $class_offering_id
 * @property int $discipline_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $start_time
 * @property string $end_time
 * @property numeric $duration_hours
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Discipline|null $discipline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession forTeacher($teacherId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereDurationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession withoutTrashed()
 */
	class ClassSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $duration_hours
 * @property string|null $prerequisites
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOffering> $classOfferings
 * @property-read int|null $class_offerings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Discipline> $disciplines
 * @property-read int|null $disciplines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScholarshipHolder> $scholarshipHolders
 * @property-read int|null $scholarship_holders_count
 * @method static \Database\Factories\CourseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDurationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course wherePrerequisites($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course withoutTrashed()
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property int $scholarship_holder_id
 * @property string|null $role
 * @property string|null $enrollment_date
 * @property string|null $completion_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @method static \Database\Factories\CourseScholarshipHolderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereEnrollmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereScholarshipHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseScholarshipHolder withoutTrashed()
 */
	class CourseScholarshipHolder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property int|null $workload
 * @property int|null $sequence_order
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOffering> $classOfferings
 * @property-read int|null $class_offerings_count
 * @property-read \App\Models\Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSession> $sessions
 * @property-read int|null $sessions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereSequenceOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline whereWorkload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discipline withoutTrashed()
 */
	class Discipline extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property string|null $header_html
 * @property string $body_html
 * @property string|null $footer_html
 * @property int|null $institution_id
 * @property int|null $unit_id
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereBodyHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereFooterHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereHeaderHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate withoutTrashed()
 */
	class DocumentTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scholarship_holder_id
 * @property int|null $project_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $activities
 * @property string $results
 * @property string $contributions
 * @property string $status
 * @property string|null $submitted_at
 * @property string|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Project|null $project
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereActivities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereContributions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereScholarshipHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinalActivityReport withoutTrashed()
 */
	class FinalActivityReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $unit_id
 * @property int $month
 * @property int $year
 * @property string $closed_at
 * @property int $closed_by_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $closedBy
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereClosedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialClosure whereYear($value)
 */
	class FinancialClosure extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $action
 * @property string $entity_type
 * @property int $entity_id
 * @property array<array-key, mixed>|null $metadata
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereUserId($value)
 */
	class FinancialLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $type
 * @property string|null $description
 * @property string|null $contact_info
 * @property string|null $address
 * @property numeric $total_amount
 * @property numeric $used_amount
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $available_amount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @method static \Database\Factories\FundingSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereContactInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource whereUsedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundingSource withoutTrashed()
 */
	class FundingSource extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $city
 * @property string $state
 * @property string|null $address
 * @property string|null $cnpj
 * @property string|null $email
 * @property string|null $website
 * @property string|null $acronym
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $logo_path
 * @property string|null $postal_code
 * @property string|null $neighborhood
 * @property string|null $complement
 * @property string|null $number
 * @property string $country
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\InstitutionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereAcronym($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereNeighborhood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Institution withoutTrashed()
 */
	class Institution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $no_class_days
 * @property numeric $delay_percent_threshold
 * @property int $check_delays_enabled
 * @property int $check_no_class_enabled
 * @property string $delay_notify_roles
 * @property string $no_class_notify_roles
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereCheckDelaysEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereCheckNoClassEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereDelayNotifyRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereDelayPercentThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereNoClassDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereNoClassNotifyRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntelligentAlertSetting whereUpdatedAt($value)
 */
	class IntelligentAlertSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scholarship_holder_id
 * @property int|null $project_id
 * @property int|null $unit_id
 * @property int|null $funding_source_id
 * @property int|null $attendance_submission_id
 * @property int $month
 * @property int $year
 * @property float $total_hours
 * @property float $amount
 * @property string $status
 * @property string|null $receipt_number
 * @property string|null $receipt_generated_at
 * @property string|null $receipt_hash
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property int|null $paid_by_user_id
 * @property string|null $payable_type
 * @property int|null $payable_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $paidBy
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $payable
 * @property-read \App\Models\Project|null $project
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAttendanceSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFundingSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReceiptGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReceiptHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereScholarshipHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withoutTrashed()
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_teacher
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScholarshipHolder> $scholarshipHolders
 * @property-read int|null $scholarship_holders_count
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereIsTeacher($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withoutTrashed()
 */
	class Position extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric|null $student_daily_rate
 * @property string $wizard_step
 * @property string $status
 * @property int $institution_id
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOffering> $classOfferings
 * @property-read int|null $class_offerings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FundingSource> $fundingSources
 * @property-read int|null $funding_sources_count
 * @property-read \App\Models\Institution|null $institution
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScholarshipHolder> $scholarshipHolders
 * @property-read int|null $scholarship_holders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units
 * @property-read int|null $units_count
 * @method static \Database\Factories\ProjectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStudentDailyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereWizardStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withoutTrashed()
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $project_id
 * @property int $funding_source_id
 * @property numeric|null $allocated_amount
 * @property numeric $used_amount
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\FundingSource|null $fundingSource
 * @property-read \App\Models\Project|null $project
 * @method static \Database\Factories\ProjectFundingSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereAllocatedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereFundingSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFundingSource whereUsedAmount($value)
 */
	class ProjectFundingSource extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $assignments
 * @property numeric|null $hourly_rate
 * @property int $weekly_workload
 * @property int $position_id
 * @property int $project_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Project|null $project
 * @method static \Database\Factories\ProjectPositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereAssignments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition whereWeeklyWorkload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectPosition withoutTrashed()
 */
	class ProjectPosition extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $project_id
 * @property int $scholarship_holder_id
 * @property int $position_id
 * @property int $weekly_workload
 * @property string|null $edital_portaria
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon $start_date
 * @property string|null $assignments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Project|null $project
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @method static \Database\Factories\ProjectScholarshipHolderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereAssignments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereEditalPortaria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereScholarshipHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder whereWeeklyWorkload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScholarshipHolder withoutTrashed()
 */
	class ProjectScholarshipHolder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $cpf
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $bank
 * @property string|null $agency
 * @property string|null $account
 * @property string|null $pix_key
 * @property int $user_id
 * @property int|null $unit_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceRecord> $attendanceRecords
 * @property-read int|null $attendance_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceSubmission> $attendanceSubmissions
 * @property-read int|null $attendance_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOffering> $classOfferings
 * @property-read int|null $class_offerings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FundingSource> $fundingSources
 * @property-read int|null $funding_sources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \App\Models\Unit|null $unit
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ScholarshipHolderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereAgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder wherePixKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolder withoutTrashed()
 */
	class ScholarshipHolder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @method static \Database\Factories\ScholarshipHolderClassOfferingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolderClassOffering newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolderClassOffering newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScholarshipHolderClassOffering query()
 */
	class ScholarshipHolderClassOffering extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $cpf
 * @property string|null $passport
 * @property string $payment_type
 * @property string|null $pix_key
 * @property string|null $bank
 * @property string|null $agency
 * @property string|null $account
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOffering> $classOfferings
 * @property-read int|null $class_offerings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentRecord> $studentRecords
 * @property-read int|null $student_records_count
 * @method static \Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePixKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withoutTrashed()
 */
	class Student extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property int $class_offering_id
 * @property int $month
 * @property int $year
 * @property int $absences
 * @property int $attended_classes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereAttendedClasses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentMonthRecord whereYear($value)
 */
	class StudentMonthRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property int $class_offering_id
 * @property int $month
 * @property int $year
 * @property numeric $amount
 * @property string $status
 * @property string|null $sent_at
 * @property string|null $paid_at
 * @property int|null $paid_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read string $computed_status
 * @property-read \App\Models\User|null $payer
 * @property-read \App\Models\Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment wherePaidBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPayment withoutTrashed()
 */
	class StudentPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property int $class_offering_id
 * @property int $total_classes
 * @property int $absences
 * @property int $attended_classes
 * @property string $status
 * @property numeric $daily_rate
 * @property numeric $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereAttendedClasses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereDailyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereTotalClasses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentRecord withoutTrashed()
 */
	class StudentRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $teacher_id
 * @property int $class_offering_id
 * @property int $discipline_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassOffering|null $classOffering
 * @property-read \App\Models\Discipline|null $discipline
 * @property-read \App\Models\User|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereClassOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeachingAssignment whereUpdatedAt($value)
 */
	class TeachingAssignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $institution_id
 * @property string $name
 * @property string|null $shortname
 * @property string $city
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $domain
 * @property string|null $cnpj
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassOffering> $classOfferings
 * @property-read int|null $class_offerings_count
 * @property-read \App\Models\Institution|null $institution
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScholarshipHolder> $scholarshipHolders
 * @property-read int|null $scholarship_holders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\UnitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit withoutTrashed()
 */
	class Unit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $institution_id
 * @property int|null $unit_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Assignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\ScholarshipHolder|null $scholarshipHolder
 * @property-read \App\Models\Unit|null $unit
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

