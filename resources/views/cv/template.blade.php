<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $firstName }} {{ $lastName }} - CV</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Arial', 'Helvetica', sans-serif;
      color: #333;
      font-size: 14px;
      background: #fff;
      line-height: 1.6;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 50px 60px;
    }
    
    /* Header Section - Photo + Name */
    .header-table {
      width: 100%;
      margin-bottom: 30px;
      border-collapse: collapse;
    }
    .photo {
      width: 200px;
      height: 200px;
      border-radius: 50%;
      overflow: hidden;
      background: #f0f0f0;
    }
    .photo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .name-title h1 {
      font-size: 38px;
      font-weight: bold;
      margin-bottom: 5px;
      line-height: 1.1;
    }
    .name-title .profession {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-top: 10px;
      text-transform: uppercase;
    }
    
    /* Summary Section */
    .summary {
      margin-bottom: 30px;
      line-height: 1.7;
      font-size: 14px;
    }
    
    /* Divider */
    .divider {
      height: 4px;
      background: #000;
      margin: 30px 0;
    }
    
    /* Three Column Layout for Contact/Skills/Tools */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .info-table td {
      vertical-align: top;
    }
    .contact-column {
      width: 33.33%;
      padding-right: 30px;
    }
    .skills-column {
      width: 33.33%;
      padding-right: 30px;
    }
    .tools-column {
      width: 33.33%;
    }
    
    /* Contact Section */
    .contact-section {
      margin-bottom: 20px;
    }
    .contact-item {
      margin-bottom: 18px;
      font-size: 14px;
      display: table;
      width: 100%;
    }
    .contact-icon {
      display: table-cell;
      width: 30px;
      vertical-align: top;
      padding-right: 8px;
    }
    .contact-text {
      display: table-cell;
      vertical-align: top;
    }
    
    /* Skills Section */
    .skills-section h2 {
      font-size: 16px;
      color: #059669;
      font-weight: bold;
      margin-bottom: 12px;
      text-transform: uppercase;
    }
    .skills-section ul {
      list-style: none;
      padding: 0;
    }
    .skills-section li {
      margin-bottom: 6px;
      padding-left: 15px;
      position: relative;
      font-size: 14px;
    }
    .skills-section li:before {
      content: "•";
      position: absolute;
      left: 0;
      font-weight: bold;
    }
    
    /* Tools Section */
    .tools-section h2 {
      font-size: 16px;
      color: #059669;
      font-weight: bold;
      margin-bottom: 12px;
      text-transform: uppercase;
    }
    .tools-section ul {
      list-style: none;
      padding: 0;
    }
    .tools-section li {
      margin-bottom: 6px;
      padding-left: 15px;
      position: relative;
      font-size: 14px;
    }
    .tools-section li:before {
      content: "•";
      position: absolute;
      left: 0;
      font-weight: bold;
    }
    
    /* Experience & Education Section (Full Width Below) */
    .experience-education-section {
      margin-top: 20px;
    }
    
    /* Experience Section */
    .section {
      margin-bottom: 25px;
    }
    .section h2 {
      font-size: 16px;
      color: #059669;
      font-weight: bold;
      margin-bottom: 12px;
      text-transform: uppercase;
    }
    .experience-item {
      margin-bottom: 15px;
    }
    .experience-header {
      margin-bottom: 3px;
      overflow: hidden;
    }
    .experience-title {
      font-size: 14px;
      font-weight: bold;
      float: left;
    }
    .experience-date {
      font-size: 13px;
      color: #666;
      float: right;
    }
    .experience-company {
      font-size: 14px;
      color: #666;
      margin-bottom: 8px;
      clear: both;
    }
    .experience-item ul {
      margin-left: 18px;
      margin-top: 6px;
    }
    .experience-item li {
      margin-bottom: 5px;
      line-height: 1.5;
      font-size: 13px;
    }
    
    /* Education Section */
    .education-item {
      margin-bottom: 10px;
    }
    .education-header {
      margin-bottom: 3px;
      overflow: hidden;
    }
    .education-degree {
      font-size: 14px;
      font-weight: bold;
      float: left;
      width: 60%;
    }
    .education-years {
      font-size: 13px;
      color: #666;
      font-weight: bold;
      float: right;
    }
    .education-institution {
      font-size: 13px;
      color: #666;
      clear: both;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header: Photo + Name + Profession -->
    <table class="header-table">
      <tr>
      <td style="width: 200px; padding-right: 40px;">
  @if(isset($photo))
    <div class="photo">
      <img src="{{ $photo }}" alt="Profile photo">
    </div>
  @endif
</td>

<!-- skills stamp badge -->
<td style="position: relative;">

<!-- Badge: Top-right -->
@if(isset($user) && $user->skillstamps->isNotEmpty())
<div style="position: absolute; top: 0; right: 0;">
    <img src="{{ $skillstampBadge }}" alt="Skillstamp Badge" style="width: 140px; height: 140px; object-fit: contain;">
</div>
@endif

          <div class="name-title">
            <h1>{{ $firstName }}<br>{{ $lastName }}</h1>
            <div class="profession">{{ $profession }}</div>
          </div>
        </td>
      </tr>
    </table>
    
    <!-- Summary -->
    <div class="summary">
      <p>{{ $summary }}</p>
    </div>
    
    <!-- Divider -->
    <div class="divider"></div>
    
    <!-- Row 1: Contact | Skills | Tools (Three Columns) -->
    <table class="info-table">
      <tr>
        <!-- Column 1: Contact Information -->
        <td class="contact-column">
          <div class="contact-section">
            <div class="contact-item">
              <div class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="5" width="18" height="14" rx="2"/>
                  <path d="M3 7l9 6 9-6"/>
                </svg>
              </div>
              <div class="contact-text">{{ $email }}</div>
            </div>
            
            <div class="contact-item">
              <div class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
              </div>
              <div class="contact-text">{{ $phoneNumber }}</div>
            </div>
            
            <div class="contact-item">
              <div class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
              </div>
              <div class="contact-text">{{ $city }}, {{ $state }}</div>
            </div>
          </div>
        </td>
        
        <!-- Column 2: Skills -->
        <td class="skills-column">
          <div class="skills-section">
            <h2>SKILLS</h2>
            <ul>
              @foreach($skills as $skill)
                <li>{{ $skill }}</li>
              @endforeach
            </ul>
          </div>
        </td>
        
        <!-- Column 3: Tools -->
        <td class="tools-column">
          <div class="tools-section">
            <h2>TOOLS</h2>
            <ul>
              @foreach($tools as $tool)
                <li>{{ $tool }}</li>
              @endforeach
            </ul>
          </div>
        </td>
      </tr>
    </table>
    
    <!-- Row 2: Experience & Education (Full Width) -->
    <div class="experience-education-section">
      <!-- Experience Section -->
      <div class="section">
        <h2>EXPERIENCE</h2>
        <div class="experience-item">
  <div style="margin-bottom: 8px;">
    <strong style="font-size: 14px;">{{ $experienceTitle ?? 'NO TITLE' }}</strong>
    <span style="font-size: 13px; color: #666; float: right;">{{ $experienceStartDate }} - {{ $experienceEndDate ?: 'Present' }}</span>
  </div>
  <div style="clear: both;"></div>
  <div class="experience-company">{{ $experienceCompany }}, {{ $experienceLocation }}</div>
  <ul>
    @php
      $descriptions = explode("\n", $experienceDescription);
    @endphp
    @foreach($descriptions as $desc)
      @if(trim($desc))
        <li>{{ trim($desc) }}</li>
      @endif
    @endforeach
  </ul>
</div>
      </div>
      
      <!-- Education Section -->
<div class="section">
  <h2>EDUCATION</h2>
  <div class="education-item">
    <div style="margin-bottom: 8px;">
      <strong style="font-size: 14px;">{{ $educationDegree }}</strong>
      <span style="font-size: 13px; color: #666; font-weight: bold; float: right;">{{ $educationStartYear }} - {{ $educationEndYear }}</span>
    </div>
    <div style="clear: both;"></div>
    <div style="font-size: 13px; color: #666;">{{ $educationInstitution }}</div>
  </div>
</div>
    </div>
  </div>
</body>
</html>